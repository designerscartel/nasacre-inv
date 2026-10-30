<?php

namespace App\Jobs;

use App\Contracts\Invoice\CreatesInvoicePdf;
use App\Mail\InvoiceForQueuing;
use App\Models\SacreInvoice;
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
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

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
        $pdf = app(CreatesInvoicePdf::class)->output($this->details['invoice'], $this->details['sacre']);
        $email = new InvoiceForQueuing($this->details['invoice'], $this->details['sacre'], $this->details['contact'], $pdf);
        $financeEmails = collect(preg_split('/[,;]+/', (string) $this->details['sacre']->finance))
            ->map('trim')
            ->filter(function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            })
            ->values()
            ->all();

        Mail::to($this->details['contact']->email)
            ->cc($financeEmails)
            ->send($email);

        SacreInvoice::updateOrCreate(
            [
                'sacre_id' => $this->details['sacre']->id,
                'invoice_id' => $this->details['invoice']->id,
                'batch' => $this->details['batch'],
            ],
            [
                'po_number' => $this->details['sacre']->po,
            ]
        );

    }


}
