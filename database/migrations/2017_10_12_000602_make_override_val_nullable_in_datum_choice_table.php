<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeOverrideValNullableInDatumChoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('datum_choice', function (Blueprint $table) {
            $table->text('override_val', 65535)->nullable()->change();
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
            $table->text('override_val', 65535)->nullable(false)->change();
        });
    }
}
