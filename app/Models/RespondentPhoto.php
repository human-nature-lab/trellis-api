<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RespondentPhoto extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $table = 'respondent_photo';

    protected $fillable = [
        'id',
        'respondent_id',
        'photo_id',
        'sort_order',
        'notes',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function delete()
    {
        Photo::where('id', $this->photo_id)
            ->delete();

        return parent::delete();
    }
}
