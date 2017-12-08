<?php

namespace App\Models;

use App\Library\DatabaseHelper;
use App\Services\UserService;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class Log extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'log';

    protected $fillable = [
        'id',
        'actor_id',
        'row_id',
        'table_name',
        'operation',
        'previous_row',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Create/update/delete row and append 'log' table if needed.
     *
     * If the optional $callback is specified, it's called with $operation set to one of ['create', 'update', 'delete'] to allow the caller to handle the write manually.
     *
     * @var array $row Key-value pairs representing database row to update/delete
     * @var array $table Database table to use
     * @var function $callback Callback of the form "function callback($row, $table, $operation) {}" (where $operation is 'create', 'update' or 'delete' and callback optionally returns truthy value if row was written and 0/false if it already existed in table) for overriding database write, pass null for callback to write row automatically
     * @return string|false|null One of the truthy values: 'create', 'update' or 'delete' (meaning row was newer than previous row), or one of the falsy values: false (if row was logged), or null (if row already existed in table or had a log entry so database wasn't updated)
     */
    public static function writeRow($row, $table, $callback = null)
    {
        return DB::transaction(function () use ($row, $table, $callback) {
            if ($table == (new self)->table) {
                return; // don't log writes involving log table
            }

            $userId = User::where('username', 'admin')->first()->id;    //TODO for now assign admin user id as actor_id until we have device_user or require login for upload sync
            // $userId = UserService::getCurrentUserId();
            //
            // if (!$userId) {
            //     return; //TODO decide how to handle database updates when no authorized user (should never happen)
            // }

            $operation = null;  // default to returning null if row wasn't written or logged
            $previousRow = (array) DB::table($table)->where('id', $row['id'])->first();

            if ($previousRow) {
                $previousTimestamp = DatabaseHelper::modifiedAt($previousRow);
                $timestamp = DatabaseHelper::modifiedAt($row);

                if ($previousTimestamp < $timestamp) {
                    $operation = (array_get($row, 'deleted_at') && !array_get($previousRow, 'deleted_at')) ? 'delete' : 'update';
                } else {
                    $previousRow = $row;    // if row is older than previous row, then swap them to log it instead
                }

                ksort($previousRow);    // log row in relatively consistent format to prevent logging it multiple times

                $log = [
                    'actor_id' => $userId,
                    'row_id' => $row['id'],
                    'table_name' => $table,
                    'operation' => $operation,
                    'previous_row' => json_encode($previousRow, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ];

                $already = static::where($log)->first();

                if (!$already) {
                    $log['id'] = Uuid::uuid4();

                    static::create($log);    // insert log entry if it's not already present
                }

                if ($previousTimestamp >= $timestamp) {
                    $operation = $already ? null : false; // return null if row already existed in log, false if row was logged due to being older than previous row
                }
            } else {
                $operation = 'create';
            }

            if ($operation) {
                if ($callback) {
                    if(in_array($callback($row, $table, $operation), [0, false], true)) { // callback returning explicit 0 or false indicates that row already exists so operation didn't happen, null indicates that callback didn't return anything so no way to know if operation happened
                        $operation = null;  // return null to indicate that row was already written
                    }
                } else {
                    if ($operation == 'create') {
                        DB::table($table)->insert($row);
                    } else {
                        if (!DB::table($table)->where('id', $row['id'])->update($row)) {    // can act as a delete since rows use soft deletes (deleted_at)
                            $operation = null;  // return null to indicate that row already existed in table
                        }
                    }
                }
            }

            return $operation;
        });
    }

    public static function onModelUpdating($model)
    {
        DB::beginTransaction();

        $row = $model->getAttributes();

        // force model write to succeed by using most-future timestamp for comparison
        $row[$model->getCreatedAtColumn()] = Carbon::createFromTimeStampUTC(PHP_INT_MAX);
        $row[$model->getUpdatedAtColumn()] = Carbon::createFromTimeStampUTC(PHP_INT_MAX);

        if (strpos(\Event::firing(), 'eloquent.deleting:') === 0) {
            $deletedAtColumn = in_array(SoftDeletes::class, class_uses($model)) ? $model->getDeletedAtColumn() : 'deleted_at';

            $row[$deletedAtColumn] = Carbon::createFromTimeStampUTC(PHP_INT_MAX);  // indicate that row is being deleted (model will perform actual delete afterwards)
        }

        static::writeRow($row, $model->getTable(), function ($row, $table, $operation) {
            // skip database write and let model handle it
        });
    }

    public static function onModelUpdated($model)
    {
        DB::commit();
    }
}
