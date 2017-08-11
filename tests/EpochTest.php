<?php

use App\Library\DatabaseHelper;
use App\Models\Epoch;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class EpochTest extends TestCase
{
    /**
     * Verify that epoch exists.
     *
     * @return void
     */
    public function testEpochExists()
    {
        try {
            $epoch = Epoch::first()->epoch;
        } catch (\Exception $e) {
            $epoch = 0;
        }

        $this->assertGreaterThanOrEqual(
            1, $epoch, "Table `epoch` not found.  Please run `php artisan migrate`."
        );
    }

    /**
     * Verify that epoch can be incremented.
     *
     * @return void
     */
    public function testEpochIncrements()
    {
        DB::transaction(function () {
            $epoch = Epoch::get();

            $this->assertEquals(
                $epoch + 1, Epoch::inc()
            );
        });
    }

    /**
     * Verify that device.epoch exists.
     *
     * @return void
     */
    public function testDeviceEpochExists()
    {
        $this->assertArrayHasKey(
            'epoch', DatabaseHelper::columns('device')
        );
    }
}
