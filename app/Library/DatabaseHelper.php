<?php

namespace App\Library;

use DB;
use PDO;

class DatabaseHelper
{
    /**
     * Returns the escaped form of an arbitrary string for safe usage directly in a raw SQL query.
     *
     * Currently alphanumeric characters, underscore and period are allowed.
     *
     * Note that this is not standardized across SQL databases: https://stackoverflow.com/a/1543309/539149
    */
    public static function escape($string)
    {
        return '`' . preg_replace('/[^0-9a-zA-Z_\.]/', '', $string) . '`';
    }

    /**
     * Calls DB::setFetchMode($mode) and returns the result of $callable, restoring the old mode afterwards.
    */
    public static function fetch($mode, $callable)
    {
        $oldMode = DB::getFetchMode();

        DB::setFetchMode($mode);

        $value = value($callable);

        DB::setFetchMode($oldMode);

        return $value;
    }

    /**
     * Returns an array of all of the tables in the current database.
    */
    public static function tables()
    {
        return static::fetch(PDO::FETCH_ASSOC, function () {
            return array_flatten(DB::select("
                show tables;
            "));
        });
    }

    /**
     * Returns array of the form:
     *
     * [
     *     "id" => [
     *         "field" => "id",
     *         "type" => "varchar(63)",
     *         "null" => "NO",
     *         "key" => "PRI",
     *         "default" => null,
     *         "extra" => "",
     *     ],
     * ]
     */
    public static function columns($table)
    {
        return static::fetch(PDO::FETCH_ASSOC, function () use ($table) {
            $escapedTable = static::escape($table);  // must escape table name in order to pass it directly to the MySQL "show columns" query

            return collect(array_map('array_change_key_case', DB::select("
                show columns from $escapedTable;
            ")))->groupBy('field')->map(function ($value) {
                return $value[0];
            })->toArray();

            // return DB::connection(config('database.default'))->getSchemaBuilder()->getColumnListing($table);
        });
    }

    /**
     * Returns array of the form:
     *
     * [
     *   0 => [
     *     "table_name" => "table_name",
     *     "column_name" => "column_name",
     *     "referenced_table_name" => "referenced_table_name",
     *     "referenced_column_name" => "referenced_column_name",
     *     "update_rule" => "NO ACTION",
     *     "delete_rule" => "NO ACTION",
     *     "constraint_name" => "fk__constraint_name",
     *   ],
     * ]
     */
    public static function foreignKeys()
    {
        return static::fetch(PDO::FETCH_ASSOC, function () {
            return DB::select("
                select
                    information_schema.key_column_usage.table_name,
                    information_schema.key_column_usage.column_name,
                    information_schema.key_column_usage.referenced_table_name,
                    information_schema.key_column_usage.referenced_column_name,
                    information_schema.referential_constraints.update_rule,
                    information_schema.referential_constraints.delete_rule,
                    information_schema.key_column_usage.constraint_name
                from
                    information_schema.key_column_usage
                join
                    information_schema.referential_constraints
                on
                    information_schema.key_column_usage.constraint_name = information_schema.referential_constraints.constraint_name
                where
                    referenced_table_schema = ?
                order by
                    table_name,
                    column_name;
            ", [config('database.connections.mysql.database')]);
        });
    }

    /**
     * Returns size of current database in bytes.
    */
    public static function sizeInBytes()
    {
        return static::fetch(PDO::FETCH_ASSOC, function () {
            return array_flatten(DB::select("
                select
                    sum(data_length + index_length) 'size'
                from
                    information_schema.tables
                where
                    table_schema = ?;
            ", [config('database.connections.mysql.database')]))[0]*1;
        });
    }

    /**
     * Returns one of the following, where the length portion (in parentheses) has been stripped:
     *
     * bigint
     * blob
     * char
     * date
     * datetime
     * decimal
     * double
     * enum
     * float
     * int
     * longblob
     * longtext
     * mediumblob
     * mediumint
     * mediumtext
     * smallint
     * text
     * time
     * timestamp
     * tinyblob
     * tinyint
     * tinytext
     * varchar
     * year
     */
    public static function unconstrainedType($type)
    {
        return strstr($type, '(', true) ?: $type;
    }

    /**
     * Returns the length portion (in parentheses) of a MySQL type or null if not present.
    */
    public static function typeLength($type)
    {
        return ((int) trim(strstr($type, '('), '()')) ?: null;
    }
}
