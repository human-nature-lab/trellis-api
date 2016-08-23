<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Study extends Model
{
	use SoftDeletes;

	public $incrementing = false;

	protected $table = 'study';

	protected $fillable = [
		'id',
		'created_at',
		'updated_at',
		'deleted_at',
		'name',
		'photo_quality',
		'root_geo',
		'census_form_master_id',
		'default_locale_id'
	];

	public function users() {
		return $this->belongsToMany('App\Models\User', 'user_study');
	}
}
