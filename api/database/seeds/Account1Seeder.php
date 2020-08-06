<?php

use Illuminate\Database\Seeder;
use App\Account;

class Account1Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $account = Account::first();
        DB::table('users')->insert([
            ['name' => 'superadmin', 'email' => 'superadmin@wms.com', 'account_id' => $account->id, 'role' => 'superadmin' , 'secret_token' => Hash::make('3pl1_Account'), 'password' => 'password'],
            ['name' => '3pl1_W01_M01', 'email' => '3pl1w01m01@wms.com', 'account_id' => $account->id, 'role' => 'warehouse_manager' , 'secret_token' => Hash::make('3pl1_W01_M01'), 'password' => 'password'],
            ['name' => '3pl1_W11_M11', 'email' =>'3pl1w11m11@wms.com', 'account_id' => $account->id, 'role' => 'warehouse_manager' , 'secret_token' => Hash::make('3pl1_W11_M11'), 'password' => 'password'],
            ['name' => '3pl1_W01_C01', 'email' =>'3pl1w01c01@wms.com', 'account_id' => $account->id, 'role' => 'client' , 'secret_token' => Hash::make('3pl1_W01_C01'), 'password' => 'password'],
            ['name' => '3pl1_W01_C02', 'email' =>'3pl1w01c02@wms.com', 'account_id' => $account->id, 'role' => 'client' , 'secret_token' => Hash::make('3pl1_W01_C02'), 'password' => 'password'],
            ['name' => '3pl1_W11_C01', 'email' =>'3pl1w11c01@wms.com', 'account_id' => $account->id, 'role' => 'client' , 'secret_token' => Hash::make('3pl1_W11_C01'), 'password' => 'password'],
            ['name' => '3pl1_W11_C02', 'email' =>'3pl1w11c02@wms.com', 'account_id' => $account->id, 'role' => 'client' , 'secret_token' => Hash::make('3pl1_W11_C02'), 'password' => 'password'],
        ]);
    }
}
