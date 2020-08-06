<?php
namespace App\Library\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use GuzzleHttp\Client as GClient;
use Config;
use JMS\Serializer\SerializerBuilder;
use App\Client;
use App\Account;

class ImportV1Clients extends BaseImportService
{

    protected $service = 'clients';

     /*
       * @Author Ashish Awasthy
       * @desc
    */
    public function fetchAllClients()
    {
        echo PHP_EOL, 'Fetching from API';
        $res = $this->gClient->get($this->apiEndPoint);
        return json_decode($res->getBody(), true);
    }

    public function parseClientInsertData($clientData)
    {
        echo PHP_EOL, 'Inserting Records in DB';
        $responseData = array_chunk($clientData, 500);
        $clientInsertDataArray = [];
        foreach ($responseData as $responseDataClient) {
            foreach ($responseDataClient as $client) {
                $client['address1'] = $client['address_1'];
                $client['address2'] = $client['address_2'];
                $client['created_at'] = date('d-m-y h:i:s');
                $client['updated_at'] = date('d-m-y h:i:s');
                unset($client['address_1'], $client['address_2']);
                $client['account_id'] = 1;
                try {
                    $clientRecord = Client::where('client_code', $client['client_code'])->first();
                    if ($clientRecord instanceof Client && isset($clientRecord->name)) {
                        echo PHP_EOL, 'Client "', $clientRecord->name, '" Already exist DB insertion skipped.';
                    } else {
                        echo PHP_EOL, print_r($client, true);
                        $clientInsertDataArray[] = $client;
                    }
                } catch (\Exception $e) {
                    echo( $e->getMessage());
                }
            }
        }

        try {
            Client::insert($clientInsertDataArray);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
