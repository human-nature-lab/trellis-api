<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeleteProcedureToEpoch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<EOT
create trigger trigger_epoch_before_update before update on epoch for each row
begin
    call `sft_dlt.cscd`();
end;
EOT
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared(<<<EOT
drop trigger if exists trigger_epoch_before_update;
EOT
        );
    }
}
