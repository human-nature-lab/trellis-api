<?php

namespace App\Library;

use Carbon\Carbon;

class TimeHelper
{
    /**
     * Accepts a unix timestamp or date/time string and returns an ISO 8601 string in UTC (yyyy-MM-ddTHH:mm:ss.SSSSSSZ) such as "2017-07-07T20:25:45.123456Z".
     *
     * Optionally accepts millisecond or microsecond unix timestamp (such as used by SQLite) if string is a number longer than 10 characters:
     *
     * timestamp: 1499464197
     * decimal: 1499464197.123456
     * milliseconds: 1499464197123
     * microseconds: 1499464197123456
     *
     * Returns current UTC time if $time is not specified.
    */
    public static function utc($time = null)
    {
        if (is_numeric($time)) {
            $integer = substr((int) $time, 0, 10);
            $decimal = ltrim(substr($time, strlen($integer)), '.,');
            $timestamp = $integer . '.' . str_pad(substr(round(0 . '.' . $decimal, 6), 2), 6, 0);
            $dateTime = Carbon::createFromFormat('U.u', $timestamp, 'UTC');
        } else {
            $dateTime = new Carbon($time);
        }

        return $dateTime->format("Y-m-d\TH:i:s.u\Z");
    }
}
