<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEpochTable extends Migration
{
    protected $table = 'epoch';
    protected $field = 'epoch';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            Schema::create($this->table, function (Blueprint $table) {
                $table->bigInteger($this->field)->unsigned();
            });

            DB::unprepared(<<<EOT
CREATE TRIGGER trigger_{$this->table}_before_insert
BEFORE INSERT ON {$this->table}
FOR EACH ROW
BEGIN
    IF (SELECT COUNT(*) FROM {$this->table}) >= 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Table `{$this->table}` must have exactly 1 row';
    END IF;
END;
EOT
);

// //NOTE due to a bug/feature of MySQL, before-update-triggers set last_insert_id() to 0 even if the statement calls last_insert_id(<value>).  they also complicate SQL dump imports.  leaving this update trigger disabled for now
// CREATE TRIGGER trigger_{$this->table}_before_update
// BEFORE UPDATE ON {$this->table}
// FOR EACH ROW
// BEGIN
//     IF NEW.{$this->field} != (OLD.{$this->field} + 1) THEN
//         SIGNAL SQLSTATE '45000'
//         SET MESSAGE_TEXT = 'Table field `{$this->table}.{$this->field}` can only be incremented';
//     END IF;
// END;

// //NOTE allowing deletions simplifies SQL dump imports.  leaving this delete trigger disabled for now
// CREATE TRIGGER trigger_{$this->table}_before_delete
// BEFORE DELETE ON {$this->table}
// FOR EACH ROW
// BEGIN
// 	IF (SELECT COUNT(*) FROM {$this->table}) <= 1 THEN
// 		SIGNAL SQLSTATE '45000'
//         SET MESSAGE_TEXT = 'Table `{$this->table}` must have exactly 1 row';
// 	END IF;
// END;

            DB::table($this->table)->insert([
                $this->field => 1
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop($this->table);
    }
}
