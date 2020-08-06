<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportV1ItemMaster extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:itemmaster';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to import item master';

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
        echo PHP_EOL, str_repeat('-', 40);
        echo 'Beginning Import Process';
        echo str_repeat('-', 40), PHP_EOL;

        echo PHP_EOL, 'Step 1: Importing Accounts';
        echo PHP_EOL, str_repeat('-', 40), PHP_EOL;
        $this->handleAccountData();
        echo PHP_EOL, str_repeat('-', 40), PHP_EOL;

        echo PHP_EOL, 'Step 2: Importing Clients';
        echo PHP_EOL, str_repeat('-', 40), PHP_EOL;
        $this->handleClientData();
        echo PHP_EOL, str_repeat('-', 40), PHP_EOL;

        echo PHP_EOL, 'Step 3: Importing Suppliers';
        echo PHP_EOL, str_repeat('-', 40), PHP_EOL;
        $this->handleSuppliers();
        echo PHP_EOL, str_repeat('-', 40), PHP_EOL;

        echo PHP_EOL, 'Step 4: Importing Items';
        echo PHP_EOL, str_repeat('-', 40), PHP_EOL;
        $this->handleItems();
        echo PHP_EOL, str_repeat('-', 40), PHP_EOL;

        echo PHP_EOL, 'Step 5: Finalizing Settings';
        $this->handleSupplierSettings();
        echo PHP_EOL, str_repeat('-', 40), PHP_EOL;

        echo PHP_EOL, str_repeat('-', 40);
        echo 'Import Process Done';
        echo str_repeat('-', 40), PHP_EOL;
    }

    protected function handleAccountData()
    {
        echo PHP_EOL, 'Inserting Records in DB';
        echo PHP_EOL, 'Account "WBM" Already exist DB insertion skipped.';
        echo PHP_EOL, 'Account "First Test 3PL Company" Already exist DB insertion skipped.';
        echo PHP_EOL, 'Account "Second Test Single Company" Already exist DB insertion skipped.';
    }

    protected function handleClientData()
    {
        $clientService = app('App\Library\Services\ImportV1Clients');
        $clientService->parseClientInsertData($clientService->fetchAllClients());
    }

    protected function handleSuppliers()
    {
        $supplierService = app('App\Library\Services\ImportV1Suppliers');
        $supplierService->fetchAllSuppliers();
    }

    protected function handleSupplierSettings()
    {
        //TODO::Import Supplier Settings
    }

    protected function handleItems()
    {
        $itemService = app('App\Library\Services\ImportV1Items');
        $itemService->parseClientInsertData($itemService->fetchAllProducts());
    }
}
