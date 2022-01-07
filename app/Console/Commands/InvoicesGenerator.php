<?php

namespace App\Console\Commands;

use App\Services\InvoicesService;
use Illuminate\Console\Command;

class InvoicesGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate invoices for all active users. Run it every first day of month at 4 AM';

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
        $invoicesService = new InvoicesService;
        $invoicesService->generateInvoices();
        return true;
    }
}
