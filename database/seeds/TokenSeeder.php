<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TokenSeeder extends Seeder {

	public function run() {
		DB::table('token')->insert([
			'token_id' => random_int(1, 10000),
			'user_id'   => 300,
			'token_hash' => hash('sha512', Str::random(60)),
			'key_id' => Str::random(32),
			'created_at' => new DateTime('now'),
			'updated_at' => new DateTime('now')
		]);
	}
}