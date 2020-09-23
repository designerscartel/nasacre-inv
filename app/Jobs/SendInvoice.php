<?php

namespace App\Jobs;

use App\Contracts\Invoice\CreatesInvoicePdf;
use App\Mail\InvoiceForQueuing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

/**
 * Class SendInvoice
 * @package App\Jobs
 */
class SendInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var
     */
    protected $details;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $pdf = app(CreatesInvoicePdf::class)->create($this->details['invoice'], $this->details['sacre'], $this->details['contact']);
        $email = new InvoiceForQueuing($this->details['invoice'], $this->details['sacre'], $this->details['contact'], $pdf);
        Mail::to($this->details['contact']->email)->send($email);
    }




}
