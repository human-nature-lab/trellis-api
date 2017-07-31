<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('user')->insert([
            'id'     => 300,
            'name'   => "Test Name",
            'password' => hash('sha512', "Tesla123"),
            'created_at' => new DateTime('now'),
            'updated_at' => new DateTime('now')
        ]);
    }
}
