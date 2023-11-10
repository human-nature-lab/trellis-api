<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UniqueTranslationLocale extends Migration {
  public function up() {
    $conflicts = DB::select("SELECT COUNT(*) AS c FROM translation_text GROUP BY translation_id, locale_id HAVING c > 1");
    $conflictCount = 0;
    foreach ($conflicts as $_) {
      $conflictCount += 1;
    }
    if ($conflictCount > 0) {
        throw new Exception("Non-unique translation/locale pairs found in translation_text table. Please remove these before migrating");
    }
    Schema::table('translation_text', function (Blueprint $table) {
      $table->unique(['translation_id', 'locale_id'], 'idx_translation_text_unique_translation_locale');
    });
  }

  public function down() {
    Schema::table('translation_text', function (Blueprint $table) {
      $table->dropUnique('idx_translation_text_unique_translation_locale');
    });
  }
}
