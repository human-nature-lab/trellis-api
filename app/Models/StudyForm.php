<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudyForm extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'study_form';

    protected $casts = ['sort_order' => 'integer'];

    protected $fillable = [
        'id',
        'study_id',
        'form_master_id',
        'sort_order',
        'created_at',
        'updated_at',
        'deleted_at',
        'form_type'
    ];

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
