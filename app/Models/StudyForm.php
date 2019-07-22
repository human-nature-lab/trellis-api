<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudyForm extends Pivot
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'study_form';

    protected $casts = ['sort_order' => 'integer'];

    protected $fillable = [
        'id',
        'study_id',
        'form_master_id',
        'census_type_id',
        'sort_order',
        'created_at',
        'updated_at',
        'deleted_at',
        'form_type_id'
    ];

    public function type () {
        return $this->belongsTo('App\Models\FormType');
    }

    public function form () {
        return $this->belongsTo('App\Models\Form', 'form_master_id');
    }

    public function censusType () {
        return $this->belongsTo('App\Models\CensusType');
    }

    /*
    public function delete()
    {
        // This will cause an infinite loop between
        // Form::delete and StudyForm::delete
        // Also, removing a form from one study should not
        // necessarily delete the form as it may be associated
        // with another study
        Form::where('id', $this->form_id)
            ->delete();

        return parent::delete();
    }
    */
}
