<?php
use Illuminate\Database\Seeder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        try {
            $password = $this->command->secret('Enter a password for the admin user');
            $confirmPassword = $this->command->secret('Confirm the password');

            while ($password !== $confirmPassword) {
                $this->command->error('Passwords do not match.');
                $password = $this->command->secret('Enter a password for the admin user');
                $confirmPassword = $this->command->secret('Confirm the password');
            }
            $user = [
                'id' => 'c1f277ab-e181-11e5-84c9-a45e60f0e921',
                'name' => 'Default Admin',
                'username' => 'admin',
                'password' => Hash::make($password),
                'role_id' => 'admin',
                'created_at' => new DateTime('now'),
                'updated_at' => new DateTime('now')
            ];
            try {
                DB::table('user')->insert($user);
            } catch (QueryException $e) {
                if ($e->getCode() == 23000) {
                    $this->command->info('Admin already exists. Attempting to update. ' . $e->getCode());
                    DB::table('user')->where('id', $user['id'])->update($user);
                } else {
                    $this->command->error('Error ' . $e->getCode());
                }
            }
        } catch (QueryException $e) {
            $this->command->error('Error ' . $e->getCode());
            if ($e->getCode() != 23000) {
                throw $e;   // re-throw if it's not a duplicate key exception (this works like an INSERT IGNORE statement)
            }
        }
    }
}