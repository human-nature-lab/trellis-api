<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNoOneColumnToQuestionDatum extends Migration {

    public function up () {
        Schema::table('question_datum', function (Blueprint $table) {
            $table->boolean('no_one')->nullable();
        });
    }

    public function down () {
        Schema::table('question_datum', function (Blueprint $table) {
            $table->dropColumn('no_one');
        });
    }

}
