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
        $this->assertExecutableExists(base_path() . '/' . self::MYSQL_2_SQLITE);
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
