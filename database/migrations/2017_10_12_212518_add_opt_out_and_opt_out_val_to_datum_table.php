<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOptOutAndOptOutValToDatumTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('datum', function (Blueprint $table) {
            $table->string('opt_out')->nullable()->after('sort_order');
            $table->text('opt_out_val', 65535)->nullable()->after('opt_out');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('datum', function (Blueprint $table) {
            $table->dropColumn('opt_out');
            $table->dropColumn('opt_out_val');
        });
    }
}
