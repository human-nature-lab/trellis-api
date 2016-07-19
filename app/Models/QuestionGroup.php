<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionGroup extends Model
{

	use SoftDeletes;

	public $incrementing = false;

	protected $table = 'question_group';

	protected $fillable = [
			'id',
			'created_at',
			'updated_at',
			'deleted_at',
	];
}
