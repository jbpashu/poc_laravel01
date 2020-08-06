<?php

use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (DB::table('accounts')->count() > 0) {
            return;
        }

        DB::table('accounts')->insert([
          ['type' => '3pl', 'name' => '3pl1'],
          ['type' => '3pl', 'name' => '3pl2']
        ]);
    }
}
