<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Choice extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'choice';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'choice_translation_id',
        'val'
    ];

    public function choiceTranslation()
    {
        return $this
            ->belongsTo('App\Models\Translation', 'choice_translation_id')
            ->with('translationText');
    }

    public function delete()
    {
        $childDatumChoices = DatumChoice::where('choice_id', '=', $this->id)->get();
        foreach ($childDatumChoices as $childDatumChoice) {
            $childDatumChoice->delete();
        }

        return parent::delete();
    }
}
