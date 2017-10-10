<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectionSkip extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'section_skip';

    protected $fillable = [
        'id',
        'section_id',
        'skip_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function section()
    {
        return $this
            ->belongsTo('App\Models\Section', 'section_id');
    }

    public function skip()
    {
        return $this
            ->belongsTo('App\Models\Skip', 'skip_id');
    }
}
