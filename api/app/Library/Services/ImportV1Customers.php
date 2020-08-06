<?php
namespace App\Library\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use GuzzleHttp\Client as GClient;
use Config;
use App\Client;
use App\Customer;
use App\Address;

use JMS\Serializer\SerializerBuilder;

class ImportV1Customers extends BaseImportService
{
    protected $service = 'customers';

     /*
       * Author :- Ravi Shrivastava
       * Description :- New service for importing customers from V1 API on some time interval
       * method name :- fetchAllCustomers
       * params :- None
    */
    public function fetchAllCustomers()
    {

        // set execution time
        set_time_limit(0);
        ini_set('max_execution_time', 300000);
        ini_set('memory_limit', '-1');
        date_default_timezone_set('America/Denver');

        //Fetch All Customers:

        $client = new GClient();
        $res = $this->gClient->get($this->apiEndPoint);
        $responseData =  json_decode($res->getBody(), true);

        $responseData = array_chunk($responseData, 500);
        $customerInsertDataArray = [];

        foreach ($responseData as $responseDataCustomer) {
            foreach ($responseDataCustomer as $customer) {
                try {
                    $clientId = Client::where('client_code', $customer['client_code'])->first()->id;
                    $customerRecord = Customer::where([
                        'customer_code' => $customer['customer_code'],
                        'client_id' => $clientId,
                    ])->first();

                    if ($customerRecord instanceof customer && isset($customerRecord->name)) {
                        echo PHP_EOL, 'customer "', $customerRecord->name, '" Already exist DB insertion skipped.';
                    } else {
                        echo PHP_EOL, print_r($customer, true);

                        $addressData = [
                            'first_name' => $customer['contact_name'],
                            'address_1' => $customer['address_1'],
                            'address_2' => $customer['address_2'],
                            'city' => $customer['city'],
                            'state_province' => $customer['state_province'],
                            'zipcode' => $customer['zip_code'],
                            'country' => $customer['country'],
                            'phone' => $customer['telephone_1'],
                            'email' => $customer['e_mail_address'],
                            'created_at' => date('d-m-y h:i:s'),
                            'updated_at' => date('d-m-y h:i:s'),
                        ];

                        $addressId = Address::insertGetId($addressData);

                        $customerData = [
                            'client_id' => $clientId,
                            'customer_code' => $customer['customer_code'],
                            'name' => $customer['name'],
                            'status' => $customer['yes_no_fields_1'] === 1 ? 'active' : 'inactive',
                            'address_id' => $addressId,
                            'shipping_address_id' => $addressId,
                            'billing_address_id' => $addressId,
                            'created_at' => date('d-m-y h:i:s'),
                            'updated_at' => date('d-m-y h:i:s'),
                        ];

                        $customerInsertDataArray[] = $customerData;
                    }
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            }
        }

        try {
            Customer::insert($customerInsertDataArray);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
