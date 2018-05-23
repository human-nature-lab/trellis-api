<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateQuestionDatumAndAction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('question_datum', function (Blueprint $table) {
            $table->string('section', 41);
            $table->string('page', 41);
            $table->boolean('dk_rf')->nullable();
            $table->text('dk_rf_val')->nullable();

            $table->dropColumn('opt_out');
            $table->dropColumn('opt_out_val');
        });

        Schema::table('action', function (Blueprint $table) {
            $table->dropForeign('action_action_type_id_foreign');
            $table->dropColumn('action_type_id');
            $table->dropColumn('action_text');

            $table->string('question_id', 41)->nullable()->change();
            $table->string('action_type', 41);
            $table->text('payload')->nullable();
            $table->integer('section');
            $table->integer('page');
            $table->integer('section_follow_up_repetition')->nullable();
            $table->integer('section_repetition')->nullable();

            $table->dropForeign('action_question_datum_id_foreign');
            $table->dropColumn('question_datum_id');
        });

        Schema::table('datum', function (Blueprint $table) {
            $table->dropForeign('datum_question_id_foreign');
            $table->dropColumn('question_id');
            $table->string('question_datum_id', 41)->nullable()->change();
            $table->smallInteger('event_order');
        });

        Schema::drop('action_type');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('action_type', function (Blueprint $table) {
            $table->string('id', 41)->primary();
            $table->string('name', 20);
        });

        Schema::table('action', function (Blueprint $table) {
            $table->text('action_text')->nullable();
            $table->string('action_type_id', 41)->nullable();

            $table->foreign('action_type_id')->references('id')->on('action_type');

            $table->dropForeign('action_question_id_foreign')->nullable();
            $table->dropColumn('payload');
            $table->dropColumn('action_type');
            $table->dropColumn('section');
            $table->dropColumn('page');
            $table->dropColumn('section_follow_up_repetition')->nullable();
            $table->dropColumn('section_repetition')->nullable();
        });

        Schema::table('question_datum', function (Blueprint $table) {
            $table->string('opt_out', 41)->nullable();
            $table->text('opt_out_val')->nullable();
            $table->dropColumn('dk_rf');
            $table->dropColumn('dk_rf_val');
            $table->dropColumn('section');
            $table->dropColumn('page');
        });

        Schema::table('datum', function (Blueprint $table) {
            $table->string('question_id', 41)->nullable();
            $table->foreign('question_id')->references('id')->on('question');
            $table->string('question_datum_id', 41)->nullable(false)->change();
            $table->dropColumn('event_order');
        });
    }
}
