<?php
use Illuminate\Database\Seeder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        try {
            $password = 'helloworld';
            if (getenv('PASSWORD')) {
                $password = getenv('PASSWORD');
            }
            DB::table('user')->insert([
                'id' => 'c1f277ab-e181-11e5-84c9-a45e60f0e921',
                'name' => 'Default Admin',
                'username' => 'admin',
                'password' => Hash::make($password),
                'role' => 'ADMIN',
                'created_at' => new DateTime('now'),
                'updated_at' => new DateTime('now')
            ]);
        } catch (QueryException $e) {
            if ($e->getCode() != 23000) {
                throw $e;   // re-throw if it's not a duplicate key exception (this works like an INSERT IGNORE statement)
            }
        }
    }
}