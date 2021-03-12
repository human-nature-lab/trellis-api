#!/bin/bash
IGNORED_TABLES=database/ignored-tables.csv
BASE_SCHEMA=database/base.sqlite.schema.sql
BASE_INDEXES=database/base.sqlite.indexes.sql

if [ -f .env ]
then
  export $(cat .env | sed 's/#.*//g' | xargs)
fi

mysqldump -d $DUMP_CMD trellismb |
grep INDEX |
./app/Console/Scripts/mysql2sqlite/mysql2sqlite - |
sqlite3 $OUT_FILE
ignoreStr=''
while IFS="" read -r t || [ -n "$t" ]
do
  ignoreStr="$ignoreStr --ignore-table=$DB_DATABASE.$t"
done < $IGNORED_TABLES

# Just exportin the schema and indexes
DUMP_CMD="mysqldump -d --skip-triggers -u$DB_USERNAME -p$DB_PASSWORD -h$DB_HOST $ignoreStr $DB_DATABASE"

# Filter out
$DUMP_CMD | ./app/Console/Scripts/mysql2sqlite/mysql2sqlite - | grep -v INDEX > $BASE_SCHEMA
$DUMP_CMD | ./app/Console/Scripts/mysql2sqlite/mysql2sqlite - | grep INDEX > $BASE_INDEXES