<?php

namespace App\Jobs;

use App\Mail\InvoiceMail as MailInvoiceMail;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class InvoiceMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    private $invoice;

    // public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Invoice $invoice )
    {
        $this->invoice = $invoice;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (env('APP_ENV') != 'prod') return;
        
        Mail::send(new MailInvoiceMail($this->invoice));
    }
}
