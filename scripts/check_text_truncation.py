#!/usr/bin/env python3
# /// script
# requires-python = ">=3.8"
# dependencies = [
#     "pymysql>=1.0",
# ]
# ///
"""
check_text_truncation.py
========================

Find string / blob values that sit *exactly* at a MySQL type-length boundary,
i.e. values that were (or may have been) silently truncated to fit a column.

Why check boundaries and not just the column's current max
----------------------------------------------------------
A column that started life as TEXT (65,535-byte cap) may later have been
migrated to MEDIUMTEXT (16 MB) or LONGTEXT (4 GB). Any value truncated *while it
was still TEXT* now sits at 65,535 bytes inside a column whose CURRENT cap is
much larger, so a naive `LENGTH(col) >= current_max` check will never find it.

This tool therefore tests every value length against *every standard MySQL
length boundary that is <= the column's current capacity*:

    char / varchar     -> CHARACTER count (CHAR_LENGTH)  vs standard ceilings BELOW the declared width
    text / blob / etc. -> BYTE      count (LENGTH)       vs {255, 65535, 16777215, declared width}

(TEXT/BLOB caps are byte-based, so we compare bytes; CHAR/VARCHAR caps are
character-based, so we compare characters.)

The declared width of a char/varchar column is chosen deliberately, so a value
that fills it *exactly* -- e.g. a 36-char uuid in varchar(36) -- is expected,
not a truncation artifact, and is NOT flagged. Only standard ceilings strictly
below the declared width are checked for char/varchar (those imply the column
used to be smaller, e.g. values stuck at 255 inside a varchar(500)). *TEXT/*BLOB
columns have no deliberate width, so a value at the current cap IS flagged.

A "hit" means the value's length equals a known type ceiling -- a strong, but
not certain, signal of historical truncation. Eyeball the rows it reports.

The logic is fully generic: it introspects information_schema and works on any
MySQL/MariaDB database with no hard-coded table or column names.

Usage
-----
    # With uv (deps auto-installed from the inline metadata above -- no venv needed):
    uv run check_text_truncation.py \
        --host 127.0.0.1 --port 3306 --user root --password \
        [--database mydb | --schemas db1,db2] \
        [--tolerance 0] [--samples 3] \
        [--include-tables t1,t2] [--exclude-tables audit_log,big_blobs]

    # Or the classic way:
    pip install pymysql && python check_text_truncation.py ...

If neither --database nor --schemas is given, all non-system schemas are scanned.
Connection defaults also read MYSQL_HOST / MYSQL_PORT / MYSQL_USER / MYSQL_PWD /
MYSQL_DATABASE from the environment.

--tolerance N also flags values up to N units short of the boundary. Set it to 3
for utf8mb4 data: byte-wise truncation of a TEXT column stops 1-3 bytes early so
it never splits a multibyte character, leaving the value just under the boundary.

NOTE: counting at boundaries requires a full scan of each scanned table. Use
--include-tables / --exclude-tables to keep the cost bounded on large databases.
"""

import argparse
import getpass
import os
import sys
from collections import defaultdict

try:
    import pymysql
    import pymysql.cursors
except ImportError:
    sys.exit("This script needs PyMySQL.  Install it with:  pip install pymysql")


# Standard MySQL string/blob length ceilings (bytes for *TEXT/*BLOB,
# characters for the char family). 4 GB LONGTEXT/LONGBLOB cap is included for
# completeness but is effectively unreachable in practice.
STANDARD_BOUNDARIES = [255, 65535, 16777215, 4294967295]

CHAR_TYPES = {"char", "varchar"}
BYTE_TYPES = {
    "tinytext", "text", "mediumtext", "longtext",
    "tinyblob", "blob", "mediumblob", "longblob",
    "binary", "varbinary",
}
SYSTEM_SCHEMAS = ("mysql", "information_schema", "performance_schema", "sys")


def qident(name):
    """Quote a MySQL identifier, escaping embedded backticks."""
    return "`" + name.replace("`", "``") + "`"


def parse_args():
    p = argparse.ArgumentParser(
        description="Find MySQL text/blob values stuck at a type-length boundary "
                    "(likely truncated).")
    p.add_argument("--host", default=os.environ.get("MYSQL_HOST", "127.0.0.1"))
    p.add_argument("--port", type=int, default=int(os.environ.get("MYSQL_PORT", 3306)))
    p.add_argument("--user", default=os.environ.get("MYSQL_USER", "root"))
    p.add_argument("--password", nargs="?", const="", default=None,
                   help="DB password. Pass --password with no value (or an empty "
                        "value) to be prompted securely instead of exposing it on "
                        "the command line. If omitted entirely, MYSQL_PWD is used.")
    p.add_argument("--database", default=os.environ.get("MYSQL_DATABASE"),
                   help="Single schema to scan.")
    p.add_argument("--schemas",
                   help="Comma-separated list of schemas to scan (overrides --database).")
    p.add_argument("--tolerance", type=int, default=0,
                   help="Also flag values up to N units short of a boundary "
                        "(use 3 for utf8mb4). Default 0 = exact boundary only.")
    p.add_argument("--samples", type=int, default=3,
                   help="Number of example primary keys to fetch per finding "
                        "(0 to disable). Default 3.")
    p.add_argument("--include-tables",
                   help="Comma-separated table names to restrict the scan to.")
    p.add_argument("--exclude-tables",
                   help="Comma-separated table names to skip.")
    return p.parse_args()


def boundaries_for(capacity, include_declared_width=True):
    """Length ceilings to test a value against.

    include_declared_width=True (the default, used for *TEXT/*BLOB): standard
    ceilings <= capacity, plus the column's own declared width. A *TEXT value
    sitting at its current cap is a genuine truncation signal.

    include_declared_width=False (used for char/varchar): only standard ceilings
    *strictly below* the declared width. The width of a char/varchar column is
    chosen intentionally, so a value that fills it exactly -- e.g. a 36-char uuid
    in varchar(36) -- is expected, not a truncation artifact, and is skipped.
    Standard ceilings below the width are still flagged because they imply the
    column was once smaller (e.g. values stuck at 255 inside a varchar(500))."""
    if not capacity:
        return []
    if include_declared_width:
        bset = {b for b in STANDARD_BOUNDARIES if b <= capacity}
        bset.add(int(capacity))
    else:
        bset = {b for b in STANDARD_BOUNDARIES if b < capacity}
    return sorted(bset)


def fetch_columns(cur, schemas, include, exclude):
    sql = """
        SELECT TABLE_SCHEMA, TABLE_NAME, COLUMN_NAME, DATA_TYPE, COLUMN_TYPE,
               CHARACTER_MAXIMUM_LENGTH, CHARACTER_OCTET_LENGTH
        FROM information_schema.COLUMNS
        WHERE DATA_TYPE IN ('char','varchar','tinytext','text','mediumtext',
                            'longtext','tinyblob','blob','mediumblob','longblob',
                            'binary','varbinary')
    """
    params = []
    if schemas:
        sql += " AND TABLE_SCHEMA IN (%s)" % ",".join(["%s"] * len(schemas))
        params += schemas
    else:
        sql += " AND TABLE_SCHEMA NOT IN (%s)" % ",".join(["%s"] * len(SYSTEM_SCHEMAS))
        params += list(SYSTEM_SCHEMAS)
    if include:
        sql += " AND TABLE_NAME IN (%s)" % ",".join(["%s"] * len(include))
        params += include
    if exclude:
        sql += " AND TABLE_NAME NOT IN (%s)" % ",".join(["%s"] * len(exclude))
        params += exclude
    sql += " ORDER BY TABLE_SCHEMA, TABLE_NAME, ORDINAL_POSITION"
    cur.execute(sql, params)
    return cur.fetchall()


def fetch_pk_columns(cur, schema, table):
    cur.execute(
        """
        SELECT COLUMN_NAME
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND INDEX_NAME = 'PRIMARY'
        ORDER BY SEQ_IN_INDEX
        """,
        (schema, table),
    )
    return [r["COLUMN_NAME"] for r in cur.fetchall()]


def build_checks(col):
    """Return list of (column_name, boundary, length_fn) for one column row."""
    dtype = col["DATA_TYPE"]
    if dtype in CHAR_TYPES:
        length_fn = "CHAR_LENGTH"
        capacity = col["CHARACTER_MAXIMUM_LENGTH"]
        bounds = boundaries_for(capacity, include_declared_width=False)
    else:
        length_fn = "LENGTH"  # bytes
        capacity = col["CHARACTER_OCTET_LENGTH"]
        bounds = boundaries_for(capacity)
    return [(col["COLUMN_NAME"], b, length_fn) for b in bounds]


def scan_table(cur, schema, table, checks, tolerance):
    """One full scan of the table; returns {check_index: count_at_boundary}."""
    select_parts = []
    for i, (colname, boundary, length_fn) in enumerate(checks):
        lo = boundary - tolerance
        select_parts.append(
            "SUM(CASE WHEN {fn}({col}) BETWEEN {lo} AND {hi} THEN 1 ELSE 0 END) AS c{i}"
            .format(fn=length_fn, col=qident(colname), lo=lo, hi=boundary, i=i)
        )
    sql = "SELECT {cols} FROM {sch}.{tbl}".format(
        cols=", ".join(select_parts),
        sch=qident(schema), tbl=qident(table),
    )
    cur.execute(sql)
    row = cur.fetchone()
    return {i: int(row["c%d" % i] or 0) for i in range(len(checks))}


def fetch_samples(cur, schema, table, colname, boundary, length_fn, tolerance,
                  pk_cols, limit):
    lo = boundary - tolerance
    select_cols = [qident(c) for c in pk_cols] if pk_cols else []
    select_cols.append("{fn}({col}) AS _len".format(fn=length_fn, col=qident(colname)))
    sql = (
        "SELECT {cols} FROM {sch}.{tbl} "
        "WHERE {fn}({col}) BETWEEN {lo} AND {hi} LIMIT {lim}"
    ).format(
        cols=", ".join(select_cols), sch=qident(schema), tbl=qident(table),
        fn=length_fn, col=qident(colname), lo=lo, hi=boundary, lim=limit,
    )
    cur.execute(sql)
    return cur.fetchall()


def main():
    args = parse_args()

    # --password not supplied -> fall back to env; supplied empty -> prompt
    # securely so it never lands in shell history or the process list.
    if args.password is None:
        password = os.environ.get("MYSQL_PWD", "")
    elif args.password == "":
        password = getpass.getpass("MySQL password: ")
    else:
        password = args.password

    schemas = None
    if args.schemas:
        schemas = [s.strip() for s in args.schemas.split(",") if s.strip()]
    elif args.database:
        schemas = [args.database]
    include = [t.strip() for t in args.include_tables.split(",")] if args.include_tables else None
    exclude = [t.strip() for t in args.exclude_tables.split(",")] if args.exclude_tables else None

    conn = pymysql.connect(
        host=args.host, port=args.port, user=args.user, password=password,
        database=args.database, charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor,
    )

    findings = []
    with conn.cursor() as cur:
        columns = fetch_columns(cur, schemas, include, exclude)

        # Group columns and their checks per (schema, table) so each table is
        # scanned exactly once.
        per_table = defaultdict(list)  # (schema, table) -> list of (col, boundary, fn)
        for col in columns:
            per_table[(col["TABLE_SCHEMA"], col["TABLE_NAME"])].extend(build_checks(col))

        total = len(per_table)
        for n, ((schema, table), checks) in enumerate(sorted(per_table.items()), 1):
            if not checks:
                continue
            print("  scanning %d/%d  %s.%s  (%d checks)..."
                  % (n, total, schema, table, len(checks)), file=sys.stderr)
            try:
                counts = scan_table(cur, schema, table, checks, args.tolerance)
            except pymysql.MySQLError as e:
                print("    !! skipped (%s)" % e, file=sys.stderr)
                continue

            hit_idxs = [i for i, c in counts.items() if c > 0]
            if not hit_idxs:
                continue

            pk_cols = fetch_pk_columns(cur, schema, table) if args.samples else []
            for i in hit_idxs:
                colname, boundary, length_fn = checks[i]
                samples = []
                if args.samples:
                    try:
                        samples = fetch_samples(cur, schema, table, colname,
                                                boundary, length_fn,
                                                args.tolerance, pk_cols, args.samples)
                    except pymysql.MySQLError:
                        pass
                findings.append({
                    "schema": schema, "table": table, "column": colname,
                    "boundary": boundary, "length_fn": length_fn,
                    "count": counts[i], "pk_cols": pk_cols, "samples": samples,
                })

    conn.close()
    report(findings, args.tolerance)


def report(findings, tolerance):
    if not findings:
        print("\nNo values found at any type-length boundary. "
              "(Try --tolerance 3 for utf8mb4 data.)")
        return

    findings.sort(key=lambda f: f["count"], reverse=True)
    print("\n%-40s %-22s %-8s %12s" % ("table.column", "boundary", "unit", "count_at_boundary"))
    print("-" * 90)
    for f in findings:
        unit = "chars" if f["length_fn"] == "CHAR_LENGTH" else "bytes"
        tgt = "%s.%s.%s" % (f["schema"], f["table"], f["column"])
        print("%-40s %-22s %-8s %12d"
              % (tgt[:40], f["boundary"], unit, f["count"]))

    print("\n" + "=" * 90)
    print("DETAIL")
    print("=" * 90)
    for f in findings:
        unit = "chars" if f["length_fn"] == "CHAR_LENGTH" else "bytes"
        lo = f["boundary"] - tolerance
        print("\n* %s.%s.%s  -- %d row(s) with %s length in [%d, %d] %s"
              % (f["schema"], f["table"], f["column"], f["count"],
                 f["length_fn"], lo, f["boundary"], unit))
        print("  inspect with:")
        print("    SELECT %s, %s(%s) AS len, %s"
              % (", ".join("`%s`" % c for c in f["pk_cols"]) or "*",
                 f["length_fn"], "`%s`" % f["column"], "`%s`" % f["column"]))
        print("    FROM `%s`.`%s` WHERE %s(`%s`) BETWEEN %d AND %d;"
              % (f["schema"], f["table"], f["length_fn"], f["column"], lo, f["boundary"]))
        for s in f["samples"]:
            pk_repr = ", ".join("%s=%r" % (c, s.get(c)) for c in f["pk_cols"]) or "(no PK)"
            print("      e.g. %s  len=%s" % (pk_repr, s.get("_len")))


if __name__ == "__main__":
    main()
