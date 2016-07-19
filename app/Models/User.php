<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
	use SoftDeletes;

	public $incrementing = false;

	protected $table = 'user';

	protected $fillable = [
		'id',
		'created_at',
		'updated_at',
		'deleted_at',
		'name',
		'username',
		'password'
	];
}
