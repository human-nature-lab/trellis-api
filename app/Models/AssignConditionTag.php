<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignConditionTag extends Model {

    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'assign_condition_tag';

    protected $fillable = [
        'id',
		'created_at',
		'updated_at',
		'deleted_at',
        'condition_tag_id',
        'logic',
        'scope'
    ];


    public function condition() {
        return $this
            ->belongsTo('App\Models\ConditionTag', 'condition_tag_id');
    }

    public function delete() {
        QuestionAssignConditionTag::where('assign_condition_tag_id', $this->id)
            ->delete();

        return parent::delete();
    }
}