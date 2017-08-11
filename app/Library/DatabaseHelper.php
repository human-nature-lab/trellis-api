<?php

namespace App\Library;

use Carbon\Carbon;
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
    public static function escape($string, $quote = true)
    {
        return ($quote ? '`' : '') . preg_replace('/[^0-9a-zA-Z_\.]/', '', $string) . ($quote ? '`' : '');
    }

    /**
     * Return the current database.
    */
    public static function database()
    {
        return data_get(head(DB::select('select database() from dual')), 'database()');
    }

    /**
     * Use the specified database (or current database if empty) until callable finishes, then restore the previous one.
    */
    public static function useDatabase($database, $callable)
    {
        if (!strlen($database)) {
            return value($callable);
        }

        $oldDatabase = static::database();

        try {
            DB::unprepared('use ' . static::escape($database));

            $value = value($callable);
        } catch (\Exception $e) {
            throw $e;
        } finally {
            DB::unprepared('use ' . static::escape($oldDatabase));
        }

        return $value;
    }

    /**
     * Calls DB::setFetchMode($mode) and returns the result of $callable, restoring the old mode afterwards.
    */
    public static function fetch($mode, $callable)
    {
        $oldMode = DB::getFetchMode();

        try {
            DB::setFetchMode($mode);

            $value = value($callable);
        } catch (\Exception $e) {
            throw $e;
        } finally {
            DB::setFetchMode($oldMode);
        }

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
     * Given a column from static::columns(), return schema description compatible with https://github.com/mojopollo/laravel-json-schema of the form:
     *
     * "type(length):unique|nullable|default(value)|..."
     *
     * For example:
     *
     * "string"
     * "integer"
     * "date"
     * "string:unique"
     * "integer:nullable"
     * "string:default('John Doe')"
     * "text:nullable"
     * "string(30):unique"
     * "integer:nullable:default(18)"
     *
     * More info at:
     *
     * https://github.com/laracasts/Laravel-5-Generators-Extended/blob/master/src/Migrations/SchemaParser.php
     */
    public static function schemaJSON($column)
    {
        $typeToSchema = [
            'bigint' => 'bigInteger',
            'binary' => 'binary',
            'bit' => 'boolean',
            'blob' => 'binary',
            'bool' => 'boolean',
            'boolean' => 'boolean',
            'char' => 'char',
            'date' => 'date',
            'datetime' => 'dateTime',
            'dec' => 'decimal',
            'decimal' => 'decimal',
            'double' => 'double',
            'fixed' => 'decimal',
            'float' => 'float',
            'int' => 'integer',
            'integer' => 'integer',
            'longtext' => 'longText',
            'mediumblob' => 'binary',
            'mediumint' => 'mediumInteger',
            'mediumtext' => 'mediumText',
            'numeric' => 'decimal',
            'real' => 'double',
            'smallint' => 'integer',
            'text' => 'text',
            'time' => 'time',
            'timestamp' => 'timestamp',
            'tinyblob' => 'binary',
            'tinyint' => 'tinyInteger',
            'tinytext' => 'text',
            'varbinary' => 'binary',
            'varchar' => 'string',
            'year' => 'smallInteger',
        ];
        $type = static::unconstrainedType($column['type']);
        $schema = array_get($typeToSchema, $type, $type);

        if ($column['extra'] == 'auto_increment') {
            $schema = str_ireplace('integer', 'Increments');
        }

        $length = static::typeLength($column['type']);

        if (isset($length)) {
            $schema .= "($length)";
        }

        if (static::typeUnsigned($column['type'])) {
            $schema .= ':unsigned';
        }

        if ($column['null'] == 'YES') {
            $schema .= ':nullable';
        }

        switch ($column['key']) {
            case 'MUL':
                $schema .= ':index';
                break;

            case 'PRI':
                $schema .= ':primary';
                break;

            case 'UNI':
                $schema .= ':unique';
                break;
        }

        if (isset($column['default'])) {
            if (is_numeric($column['default'])) {
                $default = 1.0*$column['default'];
            } else {
                $default = "'" . addcslashes($column['default'], "'") . "'";
            }

            $schema .= ":default($default)";
        }

        return $schema;
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
     * Returns current database version.
    */
    public static function version()
    {
        preg_match('/^[0-9\.]+/', DB::connection()->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION), $matches);    // extract dotted decimal from versions like '5.6.28-0ubuntu0.14.04.1 (Ubuntu)'

        return array_get($matches, 0, '');
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
     * binary
     * bit
     * blob
     * bool
     * boolean
     * char
     * data
     * date
     * datetime
     * dec
     * decimal
     * double
     * double
     * fixed
     * float
     * float
     * int
     * integer
     * longtext
     * longtext
     * mediumblob
     * mediumint
     * mediumtext
     * numeric
     * real
     * smallint
     * text
     * time
     * timestamp
     * tinyblob
     * tinyint
     * tinytext
     * varbinary
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

    /**
     * Returns true if MySQL type is unsigned or false otherwise.
    */
    public static function typeUnsigned($type)
    {
        return strcasecmp(substr($type, -strlen(' unsigned')), ' unsigned') == 0;
    }

    /**
     * Returns the floating point UTC unix timestamp for when the row was updated.
     *
     * $row can be an associative array or object.
    */
    public static function modifiedAt($row)
    {
        return max(array_map(function ($field) use ($row) {
            $dateTime = data_get($row, $field);

            return $dateTime ? (new Carbon($dateTime, 'UTC'))->format('U.u')*1 : 0;
        }, ['created_at', 'updated_at', 'deleted_at']));
    }

    /**
     * Returns the floating point UTC unix timestamp for when the database was updated.
    */
    public static function databaseModifiedAt($database = null)
    {
        if (!isset($database)) {
            $database = config('database.connections')[config('database.default')]['database'];
        }

        $dateTime = DB::table('information_schema.tables')->where('table_schema', $database)->max('update_time');

        return (new Carbon($dateTime, 'UTC'))->format('U.u')*1;
    }

    /**
     * Creates a trigger that sets the table row's deleted_at field when the referenced table rows's deleted_at becomes non-null.
     * Undeleting dependent rows must be implemented in code because it's not possible to tell which rows were soft-deleted prior to cascading.
     * Note that this function requires log_bin_trust_function_creators=1 to prevent "General error: 1419 You do not have the SUPER privilege and binary logging is enabled".
     *
     * Example: createSoftDeleteTrigger('user_addresses', 'user_id', 'users', 'id') soft-deletes any user_addresses where user_addresses.user_id = users.id when users are soft-deleted.
     */
    public static function createSoftDeleteTrigger($table, $column, $referencedTable, $referencedColumn, $triggerName = null, $dropTriggerIfExists = true)
    {
        if (!isset($triggerName)) {
            $triggerName = $referencedTable . '_' . $table . '_cascade';
        }

        $escapedTable = DatabaseHelper::escape($table);
        $escapedColumn = DatabaseHelper::escape($column);
        $escapedReferencedTable = DatabaseHelper::escape($referencedTable);
        $escapedReferencedColumn = DatabaseHelper::escape($referencedColumn);
        $escapedTriggerName = DatabaseHelper::escape($triggerName);

        if ($dropTriggerIfExists) {
            DB::unprepared(<<<EOT
drop trigger if exists $escapedTriggerName;
EOT
            );
        }

        DB::unprepared(<<<EOT
create trigger $escapedTriggerName after update on $escapedReferencedTable for each row begin
    if old.deleted_at is null and new.deleted_at is not null then
        update $escapedTable
        set deleted_at = new.deleted_at
        where $escapedTable.$escapedColumn = new.$escapedReferencedColumn
        and deleted_at is null;
    end if;
end;
EOT
        );
    }

    /**
     * Drops a trigger that sets the table row's deleted_at field when the referenced table rows's deleted_at becomes non-null.
     * Undeleting dependent rows must be implemented in code because it's not possible to tell which rows were soft-deleted prior to cascading.
     * Note that this function requires log_bin_trust_function_creators=1 to prevent "General error: 1419 You do not have the SUPER privilege and binary logging is enabled".
     */
    public static function dropSoftDeleteTrigger($table, $column, $referencedTable, $referencedColumn, $triggerName = null, $dropTriggerIfExists = true)
    {
        if (!isset($triggerName)) {
            $triggerName = $referencedTable . '_' . $table . '_cascade';
        }

        $escapedTriggerName = DatabaseHelper::escape($triggerName);

        if ($dropTriggerIfExists) {
            DB::unprepared(<<<EOT
drop trigger if exists $escapedTriggerName;
EOT
            );
        } else {
            DB::unprepared(<<<EOT
drop trigger $escapedTriggerName;
EOT
            );
        }
    }
}
