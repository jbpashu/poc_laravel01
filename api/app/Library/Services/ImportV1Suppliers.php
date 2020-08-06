<?php
namespace App\Library\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use GuzzleHttp\Client as GClient;
use Config;
use App\Client;
use App\Supplier;
use App\SupplierSetting;

use JMS\Serializer\SerializerBuilder;

class ImportV1Suppliers extends BaseImportService
{
    protected $service = 'suppliers';

     /*
       * Author :- Ravi Shrivastava
       * Description :- New service for importing suppliers from V1 API on some time interval
       *  method name :- fetchAllSuppliers
       * params :- None
    */
    public function fetchAllSuppliers()
    {

        // set execution time
        set_time_limit(0);
        ini_set('max_execution_time', 300000);
        ini_set('memory_limit', '-1');
        date_default_timezone_set('America/Denver');

        $res = $this->gClient->get($this->apiEndPoint);
        $responseData =  json_decode($res->getBody(), true);

        $responseData = array_chunk($responseData, 500);
        $supplierInsertDataArray = [];

        foreach ($responseData as $responseDataSupplier) {
            foreach ($responseDataSupplier as $supplier) {
                try {
                    $clientId = Client::where('client_code', $supplier['client_code'])->first()->id;
                    $supplierRecord = Supplier::where([
                        'supplier_code' => $supplier['supplier_code'],
                        'client_id' => $clientId,
                    ])->first();

                    if ($supplierRecord instanceof Supplier && isset($supplierRecord->name)) {
                        echo PHP_EOL, 'Supplier "', $supplierRecord->name, '" Already exist DB insertion skipped.';
                    } else {
                        echo PHP_EOL, print_r($supplier, true);
                        $supplierData = [
                            'client_id' => $clientId,
                            'supplier_code' => $supplier['supplier_code'],
                            'name' => $supplier['name'],
                            'first_name' => $supplier['name'],
                            'last_name' => '',
                            'address_1' => $supplier['address_1'],
                            'address_2' => $supplier['address_2'],
                            'address_3' => $supplier['address_3'],
                            'address_4' => $supplier['address_4'],
                            'city' => $supplier['city'],
                            'state_province' => $supplier['state_province'],
                            'zipcode' => $supplier['zip_code'],
                            'country' => $supplier['country'],
                            'email' => '',
                            'phone' => $supplier['telephone_1'],
                            'created_at' => date('d-m-y h:i:s'),
                            'updated_at' => date('d-m-y h:i:s'),
                        ];
                        
                        $supplierInsertDataArray[] = $supplierData;
                    }
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            }
        }

        try {
            Supplier::insert($supplierInsertDataArray);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
