<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Translation extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'translation';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function translationText()
    {
        return $this
            ->hasMany('App\Models\TranslationText')
            ->with('locale');
    }

    public function getLocaleText ($localeId) {
        foreach ($this->translationText as $tt) {
            if ($tt->locale_id === $localeId) {
                return $tt->translated_text;
            }
        }
        return null;
    }

    public function getAnyLocaleText ($preferredLocaleId) {
        $text = $this->getLocaleText($preferredLocaleId);
        if (is_null($text)) {
            $text = count($this->translationText) > 0 ? $this->translationText[0] : '[No translation text exists for this translation]';
        }
        return text;
    }
}
