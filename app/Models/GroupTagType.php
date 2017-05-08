<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupTagType extends Model {

    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'group_tag_type';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'name'
    ];

    public function delete() {
        $childGroupTags = GroupTag::where('group_tag_type_id', '=', $this->id)->get();
        foreach ($childGroupTags as $childGroupTag) {
            $childGroupTag->delete();
        }

        return parent::delete();
    }
}