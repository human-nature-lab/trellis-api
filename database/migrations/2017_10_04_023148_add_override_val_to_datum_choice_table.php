<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOverrideValToDatumChoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('datum_choice', function (Blueprint $table) {
            $table->text('override_val', 65535)->after('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('datum_choice', function (Blueprint $table) {
            $table->dropColumn('override_val');
        });
    }
}
