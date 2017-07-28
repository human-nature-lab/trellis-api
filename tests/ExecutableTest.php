<?php

use App\Library\DatabaseHelper;
use App\Models\Epoch;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ExecutableTest extends TestCase
{
    const MYSQL_2_SQLITE = 'app/Console/Scripts/mysql2sqlite/mysql2sqlite';

    /**
     * Verify that gzip is installed.
     *
     * @return void
     */
    public function testGZip()
    {
        $this->assertExecutableExists('gzip');
    }

    /**
     * Verify that mysql is installed.
     *
     * @return void
     */
    public function testMySQL()
    {
        $this->assertExecutableExists('mysql');
    }

    /**
     * Verify that mysql2sqlite is installed.
     *
     * @return void
     */
    public function testMySQL2SQLite()
    {
        $mysql2sqlite = base_path() . '/' . self::MYSQL_2_SQLITE;

        $this->assertExecutableExists($mysql2sqlite, "echo | $mysql2sqlite -"); // pipe empty string to mysql2sqlite
    }

    /**
     * Verify that mysqldump is installed.
     *
     * @return void
     */
    public function testMySQLDumpExists()
    {
        $this->assertExecutableExists('mysqldump');
    }
}
