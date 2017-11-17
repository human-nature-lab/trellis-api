<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCanEnumeratorAddChildToGeoTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('geo_type', function (Blueprint $table) {
            $table->boolean('can_enumerator_add_child')->default(0)->after('can_enumerator_add');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('geo_type', function (Blueprint $table) {
            $table->dropColumn('can_enumerator_add_child');
        });
    }
}
