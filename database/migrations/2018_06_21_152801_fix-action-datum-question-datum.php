<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixActionDatumQuestionDatum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('action', function (Blueprint $table) {
            $table->dropForeign('action_action_type_id_foreign');
            $table->dropColumn('action_type_id');
            $table->renameColumn('action_text', 'payload');

            $table->string('interview_id', 41);
            $table->integer('section_follow_up_repetition')->nullable();
            $table->integer('section_repetition')->nullable();

            $table->foreign('interview_id')->references('id')->on('interview');
        });

        Schema::create('question_datum', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->integer('section_repetition');
            $table->string('follow_up_datum_id', 41)->nullable()->index('fk__question_datum__datum_idx');
            $table->string('question_id', 41)->index('fk__question_datum__question_idx');
            $table->string('survey_id', 41)->nullable()->index('fk__question_datum__survey_idx');

            $table->dateTime('answered_at')->nullable();
            $table->dateTime('skipped_at')->nullable();
            $table->boolean('dk_rf')->nullable();
            $table->text('dk_rf_val')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->dateTime('deleted_at')->nullable();

            $table->foreign('follow_up_datum_id')->references('id')->on('datum');
            $table->foreign('question_id')->references('id')->on('question');
            $table->foreign('survey_id')->references('id')->on('survey');
        });

        Schema::table('datum', function (Blueprint $table) {
            $table->dropForeign('datum_preload_id_foreign');

            $table->dropColumn('preload_id');
            $table->dropColumn('opt_out');
            $table->dropColumn('opt_out_val');
            $table->dropColumn('repetition');

            $table->smallInteger('event_order');
            $table->string('question_datum_id', 41);
            $table->string('geo_id', 41)->nullable();
            $table->string('edge_id', 41)->nullable();
            $table->string('photo_id', 41)->nullable();

            $table->foreign('question_datum_id')->references('id')->on('question_datum');
            $table->foreign('geo_id')->references('id')->on('geo');
            $table->foreign('edge_id')->references('id')->on('edge');
            $table->foreign('photo_id')->references('id')->on('photo');
        });

        Schema::dropIfExists('action_type');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('action_type', function (Blueprint $table) {
            $table->string('id', 41);
            $table->string('name');
        });
        Schema::table('action', function (Blueprint $table) {
            $table->dropForeign('action_interview_id_foreign');

            $table->dropColumn('section_repetition');
            $table->dropColumn('section_follow_up_repetition');
            $table->dropColumn('interview_id');

            $table->renameColumn('payload', 'action_text');
            $table->string('action_type_id', 41)->nullabe();
            $table->foreign('action_type_id')->references('id')->on('action_type');

        });
        Schema::table('datum', function (Blueprint $table) {
            $table->dropForeign('datum_photo_id_foreign');
            $table->dropForeign('datum_edge_id_foreign');
            $table->dropForeign('datum_geo_id_foreign');
            $table->dropForeign('datum_question_datum_id_foreign');

            $table->dropColumn('photo_id');
            $table->dropColumn('edge_id');
            $table->dropColumn('geo_id');
            $table->dropColumn('question_datum_id');
            $table->dropColumn('event_order');

            $table->smallInteger('repetition');
            $table->text('opt_out_val');
            $table->string('opt_out')->nullable();
            $table->string('preload_id', 41)->nullabe();

            $table->foreign('preload_id')->references('id')->on('preload');
        });
        Schema::dropIfExists('question_datum');
    }
}
