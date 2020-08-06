<?php

use Illuminate\Database\Seeder;
use App\Account;
use App\Warehouse;
use App\User;
use App\Client;

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

        // Inserting users
        DB::table('users')->insert([
            ['name' => 'superadmin', 'email' => 'superadmin@wms.com', 'account_id' => $account->id, 'role' => 'superadmin' , 'secret_token' => Hash::make('3pl1_Account'), 'password' => 'password']
        ]);

        // creating warehouse 01 ,it's manager and clients for 3pl1
        $W01M013pl1Id = User::insertGetId( ['name' => '3pl1_W01_M01', 'email' => '3pl1w01m01@wms.com', 'account_id' => $account->id, 'role' => 'warehouse_manager' , 'secret_token' => Hash::make('3pl1_W01_M01'), 'password' => 'password']);
        $w13pl1Id = Warehouse::insertGetId([
            "name" => "3PL1_W01",
            "manager_id" => $W01M013pl1Id
        ]);

        $W01C013pl1Id = User::insertGetId(['name' => '3pl1_W01_C01', 'email' =>'3pl1w01c01@wms.com', 'account_id' => $account->id, 'role' => 'client' , 'secret_token' => Hash::make('3pl1_W01_C01'), 'password' => 'password']);

        $W01Client1Id = Client::insertGetId([
            "name" => "3pl1_W01_C01",
            "user_id" => $W01C013pl1Id
        ]);

        // orders for client1 and warehouse1
        DB::table('orders')->insert([
            [
                "order_id" => 1001, "order_date" => "20200415","backorder_id" => 0, "order_reference_1" => "101101",
                "requested_date" => "20200617", "status_text" => "Complete","status_code" => 1,
                "client_id" => $W01Client1Id, "warehouse_id" => $w13pl1Id 
            ],
            [
                "order_id" => 1002, "order_date" => "20200415","backorder_id" => 0, "order_reference_1" => "10110",
                "requested_date" => "20200617", "status_text" => "Complete","status_code" => 1,
                "client_id" => $W01Client1Id, "warehouse_id" => $w13pl1Id 
            ],
        ]);

        $W01C023pl1Id = User::insertGetId(['name' => '3pl1_W01_C02', 'email' =>'3pl1w01c02@wms.com', 'account_id' => $account->id, 'role' => 'client' , 'secret_token' => Hash::make('3pl1_W01_C02'), 'password' => 'password']);

        $W01Client2Id = Client::insertGetId([
            "name" => "3pl1_W01_C02",
            "user_id" => $W01C023pl1Id
        ]);

        // orders for client2 and warehouse1
        DB::table('orders')->insert([
            [
                "order_id" => 1003, "order_date" => "20200415","backorder_id" => 0, "order_reference_1" => "101101",
                "requested_date" => "20200617", "status_text" => "Complete","status_code" => 1,
                "client_id" => $W01Client2Id, "warehouse_id" => $w13pl1Id 
            ],
            [
                "order_id" => 1004, "order_date" => "20200415","backorder_id" => 0, "order_reference_1" => "10110",
                "requested_date" => "20200617", "status_text" => "Complete","status_code" => 1,
                "client_id" => $W01Client2Id, "warehouse_id" => $w13pl1Id 
            ],
        ]);


        
        // creating warehouse 11 and manager for 3pl1
        $W11M113pl1Id = User::insertGetId( ['name' => '3pl1_W11_M11', 'email' =>'3pl1w11m11@wms.com', 'account_id' => $account->id, 'role' => 'warehouse_manager' , 'secret_token' => Hash::make('3pl1_W11_M11'), 'password' => 'password']);

        $w23pl1Id = Warehouse::insertGetId([
            "name" => "3PL1_W11",
            "manager_id" => $W11M113pl1Id
        ]);

        $W11C013pl1Id = User::insertGetId(['name' => '3pl1_W11_C01', 'email' =>'3pl1w11c01@wms.com', 'account_id' => $account->id, 'role' => 'client' , 'secret_token' => Hash::make('3pl1_W11_C01'), 'password' => 'password']);

        $W11Client1Id = Client::insertGetId([
            "name" => "3pl1_W11_C01",
            "user_id" => $W11C013pl1Id
        ]);

        // orders for client1 and warehouse2
        DB::table('orders')->insert([
            [
                "order_id" => 1005, "order_date" => "20200415","backorder_id" => 0, "order_reference_1" => "101101",
                "requested_date" => "20200617", "status_text" => "Complete","status_code" => 1,
                "client_id" => $W11Client1Id, "warehouse_id" => $w23pl1Id 
            ],
            [
                "order_id" => 1006, "order_date" => "20200415","backorder_id" => 0, "order_reference_1" => "10110",
                "requested_date" => "20200617", "status_text" => "Complete","status_code" => 1,
                "client_id" => $W11Client1Id, "warehouse_id" => $w23pl1Id 
            ],
        ]);


        $W11C023pl1Id = User::insertGetId(['name' => '3pl1_W11_C02', 'email' =>'3pl1w11c02@wms.com', 'account_id' => $account->id, 'role' => 'client' , 'secret_token' => Hash::make('3pl1_W11_C02'), 'password' => 'password']);

        $W11Client2Id = Client::insertGetId([
            "name" => "3pl1_W11_C02",
            "user_id" => $W11C023pl1Id
        ]);

        // orders for client2 and warehouse2
        DB::table('orders')->insert([
            [
                "order_id" => 1007, "order_date" => "20200415","backorder_id" => 0, "order_reference_1" => "101101",
                "requested_date" => "20200617", "status_text" => "Complete","status_code" => 1,
                "client_id" => $W11Client2Id, "warehouse_id" => $w23pl1Id 
            ],
            [
                "order_id" => 1008, "order_date" => "20200415","backorder_id" => 0, "order_reference_1" => "10110",
                "requested_date" => "20200617", "status_text" => "Complete","status_code" => 1,
                "client_id" => $W11Client2Id, "warehouse_id" => $w23pl1Id 
            ],
        ]);



    }
}
