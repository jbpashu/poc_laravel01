<?php
namespace App\Library\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use GuzzleHttp\Client as GClient;
use Config;
use JMS\Serializer\SerializerBuilder;

class BaseImportService
{
    protected $apiCredentials = [];

    protected $apiEndPoint;

    protected $seralizer;

    /**
    * service name
    * @type string
    * @description Child Class needs to override this
    */
    protected $service;

    protected $gClient;

    public function __construct()
    {
        $this->apiCredentials = Config::get('app.wms');
        $this->seralizer      = SerializerBuilder::create()->build();
        $this->gClient        = new GClient();
        $this->apiEndPoint    = $this->createFullAPIEndpoint();
    }

    protected function createFullAPIEndpoint()
    {
        //create Full API End Point URL
        return sprintf(
            '%s%s/%s?key=%s',
            $this->apiCredentials['endpoint'],
            $this->apiCredentials['env'],
            $this->service,
            $this->apiCredentials['key']
        );
    }
}
