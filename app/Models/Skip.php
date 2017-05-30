<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Skip extends Model {

    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'skip';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'show_hide',
        'any_all',
        'precedence'
    ];

    public function conditions() {
        return $this
            ->belongsToMany('App\Models\ConditionTag', 'skip_condition_tag')
            ->whereNull('skip_condition_tag.deleted_at')
            ->withPivot('skip_id')
            ->withTimestamps();
    }

    public function delete() {
        SkipConditionTag::where('skip_id', $this->id)
            ->delete();

        QuestionGroupSkip::where('skip_id', $this->id)
            ->delete();

        return parent::delete();
    }
}