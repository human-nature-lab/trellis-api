<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAssociatedRespondentToRespondentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('respondent', function (Blueprint $table) {
            $table->string('associated_respondent_id', 41)->nullable();
            $table->foreign('associated_respondent_id', 'fk__respondent_associated_respondent__idx')->references('id')->on('respondent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('respondent', function (Blueprint $table) {
            $table->dropForeign('fk__respondent_associated_respondent__idx');
            $table->dropColumn('associated_respondent_id');
        });
    }
}
