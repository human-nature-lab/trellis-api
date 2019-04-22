<?php

use App\Models\Report;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStudyIdFormIdToReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $reportCount = Report::count();
        if ($reportCount > 0) {
            throw new Exception("The reports table should be truncated before running this migration");
        }

        Schema::table('report', function (Blueprint $table) {
            $table->dropColumn('report_id');

            $table->string('study_id', 41);
            $table->string('form_id', 41)->nullable();

            $table->foreign('study_id', 'fk__study_id_study__idx')->references('id')->on('study');
            $table->foreign('form_id', 'fk__form_id_form__idx')->references('id')->on('form');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report', function (Blueprint $table) {
            $table->dropForeign('fk__study_id_study__idx');
            $table->dropForeign('fk__form_id_form__idx');

            $table->dropColumn(['study_id', 'form_id']);

            $table->string('report_id');
        });
    }
}
