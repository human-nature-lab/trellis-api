<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Token extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'token';

    protected $fillable = [
        'id',
        'user_id',
        'token_hash',
        'key_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'user_id', 'user_id');
    }

    public function key()
    {
        return $this->hasOne('App\Models\Key', 'key_id', 'key_id');
    }

    public static function createHash()
    {
        return hash('sha512', Str::random(60));
    }
}
