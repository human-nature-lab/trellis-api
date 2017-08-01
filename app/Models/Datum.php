<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Datum extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'datum';

    protected $fillable = [
        'id',
        'name',
        'val',
        'choice_id',
        'survey_id',
        'question_id',
        'repetition',
        'parent_datum_id',
        'datum_type_id',
        'sort_order',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function delete()
    {
        $childDatumChoices = DatumChoice::where('datum_id', '=', $this->id)->get();
        foreach ($childDatumChoices as $childDatumChoice) {
            $childDatumChoice->delete();
        }

        return parent::delete();
    }
}
