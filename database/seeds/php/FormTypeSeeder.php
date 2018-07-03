<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormTypeSeeder extends Seeder
{
    public function run () {
        $types = ['data-collection', 'census', 'default_census'];
        DB::table('form_type')->insert(array_map(function ($n, $i) {
            return [
                'id' => $i,
                'name' => $n
            ];
        }, $types, array_keys($types)));
    }
}