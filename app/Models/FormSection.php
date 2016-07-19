<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormSection extends Model {

	use SoftDeletes;

	public $incrementing = false;

	protected $table = 'form_section';

	protected $fillable = [
		'id',
		'created_at',
		'updated_at',
		'deleted_at',
		'form_id',
		'section_id',
		'sort_order',
		'is_repeated',
		'max_repetitions',
		'repeat_prompt'
	];
}