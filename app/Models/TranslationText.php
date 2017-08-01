<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TranslationText extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'translation_text';

    protected $fillable = [
        'id',
        'translation_id',
        'locale_id',
        'translated_text',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function locale()
    {
        return $this->belongsTo('App\Models\Locale', 'locale_id');
    }
}
