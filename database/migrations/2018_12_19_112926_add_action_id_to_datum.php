<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActionIdToDatum extends Migration {
    public function up () {
        Schema::table('action', function (Blueprint $table) {
            $table->string('follow_up_action_id', 41)->nullable();
            $table->bigInteger('random_sort_order');
            $table->integer('sort_order');
        });

        Schema::table('action', function (Blueprint $table) {
            $table->foreign('follow_up_action_id', 'fk__action_follow_up_action__idx')->references('id')->on('action');
        });

        Schema::table('datum', function (Blueprint $table) {
            $table->string('action_id', 41)->nullable();
            $table->bigInteger('random_sort_order');
            $table->foreign('action_id', 'fk__datum_action__idx')->references('id')->on('action');
        });

        Schema::table('form_section', function (Blueprint $table) {
            $table->boolean('randomize_follow_up')->default(false);
        });
    }

    public function down () {
        Schema::table('action', function (Blueprint $table) {
            $table->dropForeign('fk__action_follow_up_action__idx');
            $table->dropColumn(['follow_up_action_id', 'random_sort_order', 'sort_order']);
        });

        Schema::table('datum', function (Blueprint $table) {
            $table->dropForeign('fk__datum_action__idx');
            $table->dropColumn(['action_id', 'random_sort_order']);
        });

        Schema::table('form_section', function (Blueprint $table) {
            $table->dropColumn('randomize_follow_up');
        });
    }
}
