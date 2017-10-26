<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Skip extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'skip';

    protected $casts = ['show_hide' => 'string', 'any_all' => 'string'];

    protected $fillable = [
        'id',
        'show_hide',
        'any_all',
        'precedence',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function conditions()
    {
        return $this
            ->hasMany('App\Models\SkipConditionTag', 'skip_id');
        /*
        return DB::table('skip_condition_tag')
            ->select('condition_tag_name as name')
            ->where('skip_id', '=', $this->id)
            ->get();
        */
        /*
        return $this
            ->belongsToMany('App\Models\ConditionTag', 'skip_condition_tag')
            ->whereNull('skip_condition_tag.deleted_at')
            ->withPivot('skip_id')
            ->withTimestamps();
        */
    }

    /*
    public function delete()
    {
        SkipConditionTag::where('skip_id', $this->id)
            ->delete();

        QuestionGroupSkip::where('skip_id', $this->id)
            ->delete();

        return parent::delete();
    }
    */
}
