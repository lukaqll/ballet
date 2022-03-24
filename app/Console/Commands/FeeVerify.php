<?php

namespace App\Console\Commands;

use App\Services\InvoicesService;
use Illuminate\Console\Command;

class FeeVerify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fee:verify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        
        $invoicesServices = new InvoicesService;
        $invoices = $invoicesServices->getExpiredInvoices();

        foreach( $invoices as $invoice ){

            echo "\n fatura $invoice->id, $invoice->value \n";
            $updated = $invoicesServices->feeVerify( $invoice );
        }

    }
}
