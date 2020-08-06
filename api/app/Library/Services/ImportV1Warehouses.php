<?php
namespace App\Library\Services;

use App\Address;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use GuzzleHttp\Client as GClient;
use Config;
use App\Client;
use App\ClientWarehouse;
use App\Warehouse;

use JMS\Serializer\SerializerBuilder;

class ImportV1Warehouses extends BaseImportService
{
    protected $service = 'invaccts';

     /*
       * Author :- Ravi Shrivastava
       * Description :- New service for importing warehouses from V1 API on some time interval
       * method name :- fetchAllWarehouses
       * params :- None
    */
    public function fetchAllWarehouses()
    {
        // set execution time
        set_time_limit(0);
        ini_set('max_execution_time', 300000);
        ini_set('memory_limit', '-1');
        date_default_timezone_set('America/Denver');

        //Fetch All Warehouses:
        $res = $this->gClient->get($this->apiEndPoint);
        $responseData =  json_decode($res->getBody(), true);

        $responseData = array_chunk($responseData, 500);
        $clientWarehouseInsertDataArray = [];

        foreach ($responseData as $responseDataWarehouse) {
            foreach ($responseDataWarehouse as $warehouse) {
                try {
                    $client = Client::where('client_code', $warehouse['client_code'])->first();
                    $warehouseRecord = Warehouse::where([
                        'warehouse_code' => $warehouse['location_id'],
                        'account_id' => $client->account_id,
                    ])->first();

                    if ($warehouseRecord instanceof Warehouse && isset($warehouseRecord->name)) {
                        echo PHP_EOL, 'Warehouse "', $warehouseRecord->name, '" Already exist DB insertion skipped.';
                    } else {
                        echo PHP_EOL, print_r($warehouse, true);

                        $addressData = [
                            'first_name' => $warehouse['contact_name'],
                            'address_1' => $warehouse['address_1'],
                            'address_2' => $warehouse['address_2'],
                            'city' => $warehouse['city'],
                            'state_province' => $warehouse['state_province'],
                            'zipcode' => $warehouse['zip_code'],
                            'country' => $warehouse['country'],
                            'phone' => $warehouse['phone_number'],
                            'created_at' => date('d-m-y h:i:s'),
                            'updated_at' => date('d-m-y h:i:s'),
                        ];

                        $addressId = Address::insertGetId($addressData);

                        $warehouseData = [
                            'account_id' => $client->account_id,
                            'warehouse_code' => $warehouse['location_id'],
                            'name' => $warehouse['location_name'],
                            'address_id' => $addressId,
                            'created_at' => date('d-m-y h:i:s'),
                            'updated_at' => date('d-m-y h:i:s'),
                        ];

                        $warehouseId = Warehouse::insertGetId($warehouseData);

                        $clientWarehouseData = [];
                        $clientWarehouseData['client_id'] = $client->id;
                        $clientWarehouseData['warehouse_id'] = $warehouseId;
                        $clientWarehouseInsertDataArray[] = $clientWarehouseData;
                    }
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            }
        }

        try {
            ClientWarehouse::insert($clientWarehouseInsertDataArray);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
