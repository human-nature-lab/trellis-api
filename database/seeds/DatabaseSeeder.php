<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        ///// seed database /////

        Model::unguard();

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        // put any seeders that require dynamic logic (like key.hash = env('APP_KEY')) into database/seeds/php directory
        foreach (glob(base_path() . '/database/seeds/php/*.php') as $filePath) {
            $this->call(basename($filePath, '.php'));
        }

        // put any SQL dumps that can use declarative statements like INSERT IGNORE into database/seeds/sql directory
        foreach (glob(base_path() . '/database/seeds/sql/*.sql') as $filePath) {
            DB::statement(file_get_contents($filePath));

            echo 'Seeded: ' . basename($filePath, '.sql') . PHP_EOL;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        Model::reguard();

        ///// check database consistency /////

        echo PHP_EOL;

        ob_start();

        Artisan::call('trellis:check:mysql:foreignkeys');

        $inconsistencies = ob_get_clean();

        if (count(json_decode($inconsistencies, true))) {
            echo json_decode('"\u274c"') . ' Database foreign keys have been checked but the following were found to be inconsistent:' . PHP_EOL;

            echo $inconsistencies;
        } else {
            echo json_decode('"\u2714"') . ' Database foreign keys have been checked and found to be consistent.' . PHP_EOL;
        }
    }
}
