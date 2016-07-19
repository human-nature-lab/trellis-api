<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectionQuestionGroup extends Model
{
	use SoftDeletes;

	public $incrementing = false;

	protected $table = 'section_question_group';

	protected $fillable = [
		'id',
		'created_at',
		'updated_at',
		'deleted_at',
		'section_id',
		'question_group_id',
		'question_group_order'
	];
}
