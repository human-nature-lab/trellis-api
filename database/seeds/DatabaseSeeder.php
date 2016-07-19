<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

    public function run() {
        Model::unguard();

        // call application seeders in proper order
        $this->call(UserSeeder::class);
        $this->call(TokenSeeder::class);

        Model::reguard();
    }
}