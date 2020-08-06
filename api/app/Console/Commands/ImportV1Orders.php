<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Symfony\Component\Console\Output\BufferedOutput;

class ImportV1Orders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import V1 orders into V3 system';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $output = new BufferedOutput;
        $this->call('import:itemmaster', [], $output);
        echo print_r($output->fetch(), true);

        app('\App\Library\Services\ImportV1Warehouses')->fetchAllWarehouses();
        app('App\Library\Services\ImportV1Customers')->fetchAllCustomers();
        app('App\Library\Services\ImportV1Orders')->fetchAllAllocatedOrders();
    }
}
