<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Epoch extends Model
{
    public $timestamps = false;

    protected $table = 'epoch';

    protected $fillable = [
        //NOTE 'epoch' must stay in the $guarded array so that increment is the only operation allowed
    ];

    protected $guarded = [
        'epoch'
    ];

    /**
     * Returns the decimal epoch as a 16 digit hex string to allow alphanumeric sorting.
     *
     * Note that PHP pins at 7fffffffffffffff even for numbers larger than (1 << 63) - 1.
     *
     * @return int
     */
    public static function hex($epoch)
    {
        return str_pad(dechex($epoch), 16, '0', STR_PAD_LEFT);
    }

    /**
     * Returns the hex epoch as a 20 digit decimal string.
     *
     * Note that PHP pins at 7fffffffffffffff even for numbers larger than (1 << 63) - 1.
     *
     * @return int
     */
    public static function dec($epoch)
    {
        return str_pad(hexdec($epoch), 20, '0', STR_PAD_LEFT);
    }

    /**
     * Returns the current epoch value.
     *
     * @return int
     */
    public static function get()
    {
        return static::orderBy('epoch', 'desc')->first()->epoch;
    }

    /**
     * Increments the current epoch value atomically and returns it.  Named inc() because the function increment() already exists on Model.
     *
     * @return int
     */
    public static function inc()
    {
        // if (($count = static::count()) > 1) {
        //     dd('Wrong row count before epoch increment: ' . $count . ', ' . (new \Exception)->getTraceAsString());
        // }

        // atomically increment epoch (expects row to exist)
        if (static::orderBy('epoch', 'desc')->limit(1)->update(['epoch' => DB::raw('last_insert_id(epoch + 1)')]) != 1) {
            throw new \Exception('Table `epoch` must have exactly 1 row');
        }

        // DB::insert('
        //     insert ignore into epoch
        //     set epoch = (select epoch from (select * from epoch limit 1) as epoch)
        //     on duplicate key update epoch = last_insert_id(epoch + 1);
        // '); // atomically increment epoch, inserting row with epoch = 0 if no rows exist

        // if (($count = static::count()) > 1) {
        //     dd('Wrong row count after epoch increment: ' . $count . ', ' . (new \Exception)->getTraceAsString());
        // }

        return DB::getPdo()->lastInsertId()*1;    // retrieve the last value inserted without executing another query (can use DB::listen() to verify)
    }
}
