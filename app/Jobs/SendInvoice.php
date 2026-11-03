<?php

namespace App\Jobs;

use App\Contracts\Invoice\CreatesInvoicePdf;
use App\Mail\InvoiceForQueuing;
use App\Models\SacreContact;
use App\Models\SacreInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

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
        $contact = $this->details['contact'] ?? new SacreContact([
            'name' => $this->details['recipient_name'],
            'email' => $this->details['recipient_email'],
        ]);
        $pdf = app(CreatesInvoicePdf::class)->output($this->details['invoice'], $this->details['sacre']);
        $email = new InvoiceForQueuing($this->details['invoice'], $this->details['sacre'], $contact, $pdf);
        $financeEmails = collect(preg_split('/[,;]+/', (string) $this->details['sacre']->finance))
            ->map('trim')
            ->filter(function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            })
            ->values()
            ->all();

        Mail::to($contact->email)
            ->cc($financeEmails)
            ->send($email);

        SacreInvoice::whereKey($this->details['sacre_invoice_id'])->update([
            'delivery_status' => 'sent',
            'sent_at' => now(),
            'failed_at' => null,
            'failure_message' => null,
        ]);

    }

    public function failed(Throwable $exception)
    {
        SacreInvoice::whereKey($this->details['sacre_invoice_id'])->update([
            'delivery_status' => 'failed',
            'failed_at' => now(),
            'failure_message' => substr($exception->getMessage(), 0, 2000),
        ]);
    }


}
