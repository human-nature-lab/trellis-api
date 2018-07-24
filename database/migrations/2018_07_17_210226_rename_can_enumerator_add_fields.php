<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameCanEnumeratorAddFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // The mysqldump -> sqlite script changes 'enum' to 'text', also, can_user_add is clearer
        Schema::table('geo_type', function (Blueprint $table) {
            $table->renameColumn('can_enumerator_add', 'can_user_add');
            $table->renameColumn('can_enumerator_add_child', 'can_user_add_child');
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
            $table->renameColumn('can_user_add', 'can_enumerator_add');
            $table->renameColumn('can_user_add_child', 'can_enumerator_add_child');
        });
    }
}
