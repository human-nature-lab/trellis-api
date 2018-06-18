<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ActionAndDatumAndQuestionDatum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_datum', function (Blueprint $table) {
            $table->string('id', 41)->primary();

            // Location information
            $table->integer('section_repetition');
            $table->string('follow_up_datum_id', 41)->nullable()->index('fk__question_datum__datum_idx');
            $table->string('question_id', 41)->index('fk__question_datum__question_idx');
            $table->string('interview_id', 41)->nullable()->index('fk__question_datum__interview_idx');

            $table->dateTime('answered_at')->nullable();
            $table->dateTime('skipped_at')->nullable();
            $table->string('opt_out')->nullable();
            $table->text('opt_out_val')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->dateTime('deleted_at')->nullable();
        });

        Schema::table('question_datum', function (Blueprint $table) {
            $table->foreign('follow_up_datum_id')->references('id')->on('datum')->onUpdate('no action')->onDelete('set null');
            $table->foreign('question_id')->references('id')->on('question')->onUpdate('no action')->onDelete('cascade');
            $table->foreign('interview_id')->references('id')->on('interview')->onUpdate('no action')->onDelete('set null');
        });

        Schema::table('action', function (Blueprint $table) {
            $table->string('question_datum_id', 41)->nullable();
            $table->foreign('question_datum_id', 'fk__action__question_datum')->references('id')->on('question_datum');
        });

        Schema::table('datum', function (Blueprint $table) {

            $table->dropForeign('datum_preload_id_foreign');
            $table->dropColumn('preload_id');
            $table->dropColumn('repetition');
            $table->dropForeign('fk__parent_datum_id__datum');
            $table->dropColumn('parent_datum_id');
            $table->dropColumn('opt_out');
            $table->dropColumn('opt_out_val');

            $table->string('question_datum_id', 41);
            $table->string('geo_id', 41)->nullable();
            $table->string('edge_id', 41)->nullable();
            $table->string('photo_id', 41)->nullable();

            $table->foreign('question_datum_id')->references('id')->on('question_datum');
            $table->foreign('geo_id')->references('id')->on('geo');
            $table->foreign('edge_id')->references('id')->on('edge');
            $table->foreign('photo_id')->references('id')->on('photo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('action', function (Blueprint $table) {
            $table->dropForeign('fk__action__question_datum');
            $table->dropColumn('question_datum_id');
        });

        Schema::table('datum', function (Blueprint $table) {
            $table->string('preload_id', 41)->nullable();
            $table->integer('repetition')->nullable();
            $table->string('parent_datum_id', 41)->nullable();
            $table->string('opt_out')->nullable();
            $table->text('opt_out_val')->nullable();

            $table->dropForeign('datum_question_datum_id_foreign');
            $table->dropForeign('datum_geo_id_foreign');
            $table->dropForeign('datum_edge_id_foreign');
            $table->dropForeign('datum_photo_id_foreign');

            $table->dropColumn('question_datum_id');
            $table->dropColumn('geo_id');
            $table->dropColumn('edge_id');
            $table->dropColumn('photo_id');

            $table->foreign('preload_id')->references('id')->on('preload')->onUpdate('no action')->onDelete('cascade');
            $table->foreign('parent_datum_id', 'fk__parent_datum_id__datum')->references('id')->on('datum')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });

        Schema::drop('question_datum');
    }
}
