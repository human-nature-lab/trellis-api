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
        'created_at',
        'updated_at',
        'deleted_at',
        'actor_id',
        'row_id',
        'table_name',
        'operation',
        'previous_row'
    ];

    /**
     * Create/update/delete row and append 'log' table if needed.
     *
     * If the optional $callback is specified, it's called with $operation set to one of ['create', 'update', 'delete'] to allow the caller to handle the write manually.
     *
     * @var array $row Key-value pairs representing database row to update/delete
     * @var array $table Database table to use
     * @var function $callback Callback of the form "function callback($row, $table, $operation) {}" (where $operation is 'create', 'update' or 'delete') for overriding database write, pass null to write row automatically
     * @return string|null One of 'create', 'update', 'delete' (meaning row was newer than previous row), or null if it was logged
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

            $operation = null;
            $previousRow = (array) DB::table($table)->where('id', $row['id'])->first();

            if ($previousRow) {
                if ($previousRow != $row) {
                    $previousTimestamp = DatabaseHelper::modifiedAt($previousRow);
                    $timestamp = DatabaseHelper::modifiedAt($row);

                    if ($previousTimestamp < $timestamp) {
                        $operation = (array_get($row, 'deleted_at') && !array_get($previousRow, 'deleted_at')) ? 'delete' : 'update';
                    } else {
                        $previousRow = $row;    // if row is older than previous row, then swap them to log it instead
                    }

                    $log = [
                        'actor_id' => $userId,
                        'row_id' => $row['id'],
                        'table_name' => $table,
                        'operation' => $operation,
                        'previous_row' => json_encode($previousRow, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    ];

                    if (!static::where($log)->first()) {
                        $log['id'] = Uuid::uuid4();

                        static::create($log);    // insert log entry if it's not already present
                    }
                }
            } else {
                $operation = 'create';
            }

            if ($operation) {
                if ($callback) {
                    $callback($row, $table, $operation);
                } else {
                    if ($operation == 'create') {
                        DB::table($table)->insert($row);
                    } else {
                        DB::table($table)->where('id', $row['id'])->update($row);
                    }
                }

                return $operation;
            }

            return null;
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
