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
     * Similar to escape() but abbreviates the string by removing non-first letter vowels and runs of the same character, then truncates the result to 64 characters to fit within a MySQL identifier.
     */
	public static function abbreviate($string, $quote = true)
    {
        return ($quote ? '`' : '') . substr(static::escape(preg_replace('/(\w)\1+/', '\\1', preg_replace('/(?<=[^_.])\B[aeiouAEIOU]/', '', $string)), false), 0, 64) . ($quote ? '`' : '');
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
     * Returns array of the form:
     *
     * [
     *     [
     *         "Trigger" => "<trigger_name>",
     *         "Event" => "INSERT|UPDATE|DELETE",
     *         "Table" => "<table_name>",
     *         "Statement" => """
     *           begin\n
     *               -- ...;\n
     *           end
     *           """,
     *         "Timing" => "BEFORE|AFTER",
     *         "Created" => "Y-m-d H:i:s",
     *         "sql_mode" => "",
     *         "Definer" => "user@domain",
     *         "character_set_client" => "<character_set>",
     *         "collation_connection" => "<collation>",
     *         "Database Collation" => "<collation>",
     *     ]
     * ]
     */
    public static function triggers($table = null)
    {
        return static::fetch(PDO::FETCH_ASSOC, function () use ($table) {
            return isset($table) ? DB::select('show triggers where `Table` = ?', [$table]) : DB::select('show triggers');
        });
    }

	/**
     * Returns array of the form:
     *
	 * [
     *     [
     *         "Db" => "<database_name>",
     *         "Name" => "<procedure_name>",
     *         "Type" => "PROCEDURE",
     *         "Definer" => "user@domain",
     *         "Modified" => "Y-m-d H:i:s",
     *         "Created" => "Y-m-d H:i:s",
     *         "Security_type" => "DEFINER",
     *         "Comment" => "",
     *         "character_set_client" => "<character_set>",
     *         "collation_connection" => "<collation>",
     *         "Database Collation" => "<collation>",
     *     ]
     * ]
     */
    public static function procedures()
    {
        return static::fetch(PDO::FETCH_ASSOC, function () {
            return DB::select('SHOW PROCEDURE STATUS where db = (SELECT DATABASE())');
        });
    }

	/**
     * Returns array of the form:
     *
	 * [
     * 	"Db" => "<database_name>",
     * 	"Name" => "<procedure_name>",
     * 	"Type" => "PROCEDURE",
     * 	"Definer" => "user@domain",
     * 	"Modified" => "Y-m-d H:i:s",
     * 	"Created" => "Y-m-d H:i:s",
     * 	"Security_type" => "DEFINER",
     * 	"Comment" => "",
     * 	"character_set_client" => "<character_set>",
     * 	"collation_connection" => "<collation>",
     * 	"Database Collation" => "<collation>",
     * ]
     */
    public static function procedure($name)
    {
        return static::fetch(PDO::FETCH_ASSOC, function () use ($name) {
            return head(DB::select('SHOW CREATE PROCEDURE ' . static::escape($name))) ?: null;
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
     * Returns the default soft delete trigger name corresponding to the specified table, column, referenced table and referenced column.
     *
     * @param  string  $table               The table from where the soft delete originates.
     * @param  string  $column              The column from where the soft delete originates.
     * @param  string  $referencedTable     The table to where the soft delete cascades.
     * @param  string  $referencedColumn    The column to where the soft delete cascades.
     * @return string                       The name of the trigger.
     */
    public static function softDeleteTriggerName($table, $column, $referencedTable, $referencedColumn)
    {
        return static::abbreviate($referencedTable . '.' . $referencedColumn . '.' . $table . '.' . $column . '.cascade', false);
    }

    /**
     * Inverse of softDeleteTriggerName().  Derives the table, column, referenced table and referenced column based on the naming convention.
     *
     * Returns array of the form:
     *
     * [
     *     [
     *         'table_name' => ,
     *         'column_name' => ,
     *         'referenced_table_name' => ,
     *         'referenced_column_name' => ,
     *     ],
     *     ...
     * ]
     *
     * Note that multiple candidates might be returned due to ambiguity with abbreviations unless $deriveFromTriggerIfExists is true, the trigger exists and it hasn't been modified.
     *
     * @param  string  $triggerName                 The name of the trigger.
     * @param  boolean $deriveFromTriggerIfExists   (Optional) whether to automatically drop the trigger if it exists.
     * @return array                                The foreign key fields corresponding to the trigger.
     */
    public static function softDeleteTriggerTableColumns($triggerName, $deriveFromTriggerIfExists = true)
    {
		$foreignKeys = static::foreignKeys();

        if($deriveFromTriggerIfExists) {
            $trigger = head(array_filter(static::triggers(), function ($trigger) use ($triggerName) {
                return $trigger['Trigger'] == $triggerName;
            })) ?: null;

            if(isset($trigger)) {
                $foreignKeys = array_filter($foreignKeys, function ($foreignKey) use ($trigger) {
                    return $foreignKey['referenced_table_name'] == $trigger['Table'];
                }); // filter foreign keys to only include the matching table name (referenced_table_name corresponds to the trigger's table because soft deletes cascade from referenced table to table)
            }
        }

		$results = [];

		foreach($foreignKeys as $foreignKey) {
			if(static::softDeleteTriggerName($foreignKey['table_name'], $foreignKey['column_name'], $foreignKey['referenced_table_name'], $foreignKey['referenced_column_name']) == $triggerName) {
				$results []= array_intersect_key($foreignKey, array_flip(['table_name', 'column_name', 'referenced_table_name', 'referenced_column_name']));
			}
		}

        // use trigger statement to derive table columns if more than one match found and trigger exists
        if(count($results) > 1 && isset($trigger)) {
            $template = '/.*?' . str_replace('###', '(.+?)', preg_replace('/\s+/', '\s+', preg_quote(<<<EOT
begin
    if old.deleted_at is null and new.deleted_at is not null then
        update `###`
        set deleted_at = new.deleted_at
        where `###`.`###` = new.`###`
        and deleted_at is null;
    end if;
end
EOT
        , '/'))) . '.*?/';

            preg_match_all($template, $trigger['Statement'], $matches);

            $match = [
                'table_name' => array_get($matches, '1.0'),
                'column_name' => array_get($matches, '3.0'),
                'referenced_table_name' => $trigger['Table'],
                'referenced_column_name' => array_get($matches, '4.0'),
            ];

            $filteredResults = array_filter($results, function ($foreignKey) use ($match) {
                return $foreignKey == $match;
            });

            if(count($filteredResults) >= 1) {
                $results = $filteredResults;    // only use filtered results if table columns could be derived from trigger statement
            }
        }

        return $results;
    }

    /**
     * Creates a trigger that sets the table row's deleted_at field when the referenced table rows's deleted_at becomes non-null.
     * Undeleting dependent rows must be implemented in code because it's not possible to tell which rows were soft-deleted prior to cascading.
     * Note that this method requires log_bin_trust_function_creators=1 to prevent "General error: 1419 You do not have the SUPER privilege and binary logging is enabled".
     *
     * Example: createSoftDeleteTrigger('user_addresses', 'user_id', 'users', 'id') soft-deletes any user_addresses where user_addresses.user_id = users.id when users are soft-deleted.
     *
     * @param  string  $table               The table from where the soft delete originates.
     * @param  string  $column              The column from where the soft delete originates.
     * @param  string  $referencedTable     The table to where the soft delete cascades.
     * @param  string  $referencedColumn    The column to where the soft delete cascades.
     * @param  string  $triggerName         (Optional) the name with which to override the default naming convention.
     * @param  boolean $dropTriggerIfExists (Optional) whether to automatically drop the trigger if it exists.
     * @return string                       The name of the trigger.
     */
    public static function createSoftDeleteTrigger($table, $column, $referencedTable, $referencedColumn, $triggerName = null, $dropTriggerIfExists = true)
    {
        if (!isset($triggerName)) {
            $triggerName = static::softDeleteTriggerName($table, $column, $referencedTable, $referencedColumn);
        }

        $escapedTable = static::escape($table);
        $escapedColumn = static::escape($column);
        $escapedReferencedTable = static::escape($referencedTable);
        $escapedReferencedColumn = static::escape($referencedColumn);
        $escapedTriggerName = static::escape($triggerName);

        if ($dropTriggerIfExists) {
            DB::unprepared(<<<EOT
drop trigger if exists $escapedTriggerName;
EOT
            );
        }

        // NOTE if updating this statement, verify that softDeleteTriggerTableColumns() still matches both the old and new versions
        DB::unprepared(<<<EOT
create trigger $escapedTriggerName after update on $escapedReferencedTable for each row
begin
    if old.deleted_at is null and new.deleted_at is not null then
        update $escapedTable
        set deleted_at = new.deleted_at
        where $escapedTable.$escapedColumn = new.$escapedReferencedColumn
        and deleted_at is null;
    end if;
end
;
EOT
        );

        return $triggerName;
    }

    /**
     * Drops a trigger that sets the table row's deleted_at field when the referenced table rows's deleted_at becomes non-null.
     * Undeleting dependent rows must be implemented in code because it's not possible to tell which rows were soft-deleted prior to cascading.
     * Note that this method requires log_bin_trust_function_creators=1 to prevent "General error: 1419 You do not have the SUPER privilege and binary logging is enabled".
     *
     * @param  string  $table               The table from where the soft delete originates.
     * @param  string  $column              The column from where the soft delete originates.
     * @param  string  $referencedTable     The table to where the soft delete cascades.
     * @param  string  $referencedColumn    The column to where the soft delete cascades.
     * @param  string  $triggerName         (Optional) the name with which to override the default naming convention.
     * @param  boolean $dropTriggerIfExists (Optional) whether to automatically drop the trigger if it exists.
     * @return string                       The name of the trigger.
     */
    public static function dropSoftDeleteTrigger($table, $column, $referencedTable, $referencedColumn, $triggerName = null, $dropTriggerIfExists = true)
    {
        if (!isset($triggerName)) {
            $triggerName = static::softDeleteTriggerName($table, $column, $referencedTable, $referencedColumn);
        }

        $escapedTriggerName = static::escape($triggerName);

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

        return $triggerName;
    }

    /**
     * Returns the default soft delete procedure name corresponding to the specified table, column, referenced table and referenced column.
     *
     * @param  string  $table               The table from where the soft delete originates.
     * @param  string  $column              The column from where the soft delete originates.
     * @param  string  $referencedTable     The table to where the soft delete cascades.
     * @param  string  $referencedColumn    The column to where the soft delete cascades.
     * @return string                       The name of the procedure.
     */
    public static function softDeleteProcedureName($table, $column, $referencedTable, $referencedColumn)
    {
        return static::abbreviate($referencedTable . '.' . $referencedColumn . '.' . $table . '.' . $column . '.cascade', false);
    }

    /**
     * Inverse of softDeleteProcedureName().  Derives the table, column, referenced table and referenced column based on the naming convention.
     *
     * Returns array of the form:
     *
     * [
     *     [
     *         'table_name' => ,
     *         'column_name' => ,
     *         'referenced_table_name' => ,
     *         'referenced_column_name' => ,
     *     ],
     *     ...
     * ]
     *
     * Note that multiple candidates might be returned due to ambiguity with abbreviations unless $deriveFromProcedureIfExists is true, the procedure exists and it hasn't been modified.
     *
     * @param  string  $procedureName               The name of the procedure.
     * @param  boolean $deriveFromProcedureIfExists (Optional) whether to automatically drop the procedure if it exists.
     * @return array                                The foreign key fields corresponding to the procedure.
     */
    public static function softDeleteProcedureTableColumns($procedureName, $deriveFromProcedureIfExists = true)
    {
		$foreignKeys = static::foreignKeys();
		$results = [];

		foreach($foreignKeys as $foreignKey) {
			if(static::softDeleteProcedureName($foreignKey['table_name'], $foreignKey['column_name'], $foreignKey['referenced_table_name'], $foreignKey['referenced_column_name']) == $procedureName) {
				$results []= array_intersect_key($foreignKey, array_flip(['table_name', 'column_name', 'referenced_table_name', 'referenced_column_name']));
			}
		}

        // use procedure statement to derive table columns if more than one match found and procedure exists
        if(count($results) > 1 && $deriveFromProcedureIfExists && ($procedure = static::procedure($procedureName))) {
            $template = '/.*?' . str_replace('###', '(.+?)', preg_replace('/\s+/', '\s+', preg_quote(<<<EOT
begin

repeat
    update `###`
    inner join `###` as `\$``###` on `###`.`###` = `\$``###`.`###`
    set `###`.deleted_at = `\$``###`.deleted_at
    where `###`.`###` is not null
    and `###`.deleted_at is null
    and `\$``###`.deleted_at is not null;
until (select row_count()) = 0 end repeat;

end
EOT
        , '/'))) . '.*?/';

            preg_match_all($template, $procedure['Create Procedure'], $matches);

            $match = [
                'table_name' => array_get($matches, '1.0'),
                'column_name' => array_get($matches, '5.0'),
                'referenced_table_name' => array_get($matches, '2.0'),
                'referenced_column_name' => array_get($matches, '7.0'),
            ];

            $filteredResults = array_filter($results, function ($foreignKey) use ($match) {
                return $foreignKey == $match;
            });

            if(count($filteredResults) >= 1) {
                $results = $filteredResults;    // only use filtered results if table columns could be derived from procedure statement
            }
        }

        return $results;
    }

    /**
     * Similar to createSoftDeleteTrigger() but updates any inconsistent rows where deleted_at is null even though the referenced table's deleted_at is not null.
     * This is necessary due to a bug/feature of MySQL that causes "ERROR 1442 (HY000): Can't update table '<your_table>' in stored function/trigger because it is already used by statement which invoked this stored function/trigger."
     * The normal use case is to use `php artisan trellis:show:mysql:foreignkeycycles` to find any triggers having cyclical foreign keys, drop the triggers, and use createSoftDeleteProcedure() instead (calling it periodically or in model events).
     *
     * @param  string  $table                   The table from where the soft delete originates.
     * @param  string  $column                  The column from where the soft delete originates.
     * @param  string  $referencedTable         The table to where the soft delete cascades.
     * @param  string  $referencedColumn        The column to where the soft delete cascades.
     * @param  string  $procedureName           (Optional) the name with which to override the default naming convention.
     * @param  boolean $dropProcedureIfExists   (Optional) whether to automatically drop the procedure if it exists.
     * @return string                           The name of the procedure.
     */
    public static function createSoftDeleteProcedure($table, $column, $referencedTable, $referencedColumn, $procedureName = null, $dropProcedureIfExists = true)
    {
        if (!isset($procedureName)) {
            $procedureName = static::softDeleteProcedureName($table, $column, $referencedTable, $referencedColumn);
        }

        $escapedTable = static::escape($table);
        $escapedColumn = static::escape($column);
        $escapedReferencedTable = static::escape($referencedTable);
        $escapedReferencedColumn = static::escape($referencedColumn);
        $escapedProcedureName = static::escape($procedureName);

        if ($dropProcedureIfExists) {
            DB::unprepared(<<<EOT
drop procedure if exists $escapedProcedureName;
EOT
            );
        }

		// prefix joined table alias with `$` to prevent ambiguity errors
		// NOTE if updating this statement, verify that softDeleteProcedureTableColumns() still matches both the old and new versions
        DB::unprepared(<<<EOT
create procedure $escapedProcedureName ()
begin

repeat
    update $escapedTable
    inner join $escapedReferencedTable as `\$`$escapedReferencedTable on $escapedTable.$escapedColumn = `\$`$escapedReferencedTable.$escapedReferencedColumn
    set $escapedTable.deleted_at = `\$`$escapedReferencedTable.deleted_at
    where $escapedTable.$escapedColumn is not null
    and $escapedTable.deleted_at is null
    and `\$`$escapedReferencedTable.deleted_at is not null;
until (select row_count()) = 0 end repeat;

end
;
EOT
        );

        return $procedureName;
    }

    /**
     * Calls the soft delete procedure created by createSoftDeleteProcedure() having the same arguments.
     *
     * @param  string  $table                   The table from where the soft delete originates.
     * @param  string  $column                  The column from where the soft delete originates.
     * @param  string  $referencedTable         The table to where the soft delete cascades.
     * @param  string  $referencedColumn        The column to where the soft delete cascades.
     * @param  string  $procedureName           (Optional) the name with which to override the default naming convention.
     * @return string                           The name of the procedure.
     */
    public static function callSoftDeleteProcedure($table, $column, $referencedTable, $referencedColumn, $procedureName = null)
    {
        if (!isset($procedureName)) {
            $procedureName = static::softDeleteProcedureName($table, $column, $referencedTable, $referencedColumn);
        }

        $escapedProcedureName = static::escape($procedureName);

        DB::select("call $escapedProcedureName");

        return $procedureName;
    }

    /**
     * Similar to dropSoftDeleteTrigger().  See createSoftDeleteProcedure() for more information.
     *
     * @param  string  $table                   The table from where the soft delete originates.
     * @param  string  $column                  The column from where the soft delete originates.
     * @param  string  $referencedTable         The table to where the soft delete cascades.
     * @param  string  $referencedColumn        The column to where the soft delete cascades.
     * @param  string  $procedureName           (Optional) the name with which to override the default naming convention.
     * @param  boolean $dropProcedureIfExists   (Optional) whether to automatically drop the procedure if it exists.
     * @return string                           The name of the procedure.
     */
    public static function dropSoftDeleteProcedure($table, $column, $referencedTable, $referencedColumn, $procedureName = null, $dropProcedureIfExists = true)
    {
        if (!isset($procedureName)) {
            $procedureName = static::softDeleteProcedureName($table, $column, $referencedTable, $referencedColumn);
        }

        $escapedProcedureName = static::escape($procedureName);

        if ($dropProcedureIfExists) {
            DB::unprepared(<<<EOT
drop procedure if exists $escapedProcedureName;
EOT
            );
        } else {
            DB::unprepared(<<<EOT
drop procedure $escapedProcedureName;
EOT
            );
        }

        return $procedureName;
    }
}
