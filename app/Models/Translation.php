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
			'deleted_at',
	];

    public function translationText() {
        return $this
            ->hasMany('App\Models\TranslationText')
            ->with('locale');
    }
}
