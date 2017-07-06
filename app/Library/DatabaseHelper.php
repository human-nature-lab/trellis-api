<?php

namespace App\Library;

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
}
