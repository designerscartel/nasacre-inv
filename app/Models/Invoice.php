<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'email',
        'subs',
        'date',
        'year',
        'from',
        'message',
        'batch'
    ];

    protected $casts = [
        'date' => 'datetime',
        'subs' => 'decimal:2',
        'year' => 'integer',
        'batch' => 'integer',
    ];

    /**
     * Return a Resource
     */
    public function toResource(?string $resourceClass = null): \Illuminate\Http\Resources\Json\JsonResource
    {
        return new \App\Http\Resources\InvoiceResource($this);
    }

    public function invoices()
    {
        return $this->hasMany(SacreInvoice::class);
    }

    public function latestDeliverySummary()
    {
        $latestBatch = max((int) $this->batch, (int) $this->invoices->max('batch'));
        $deliveries = $this->invoices->filter(function ($sacreInvoice) use ($latestBatch) {
            return (int) $sacreInvoice->batch === $latestBatch;
        })->values();

        if ($deliveries->isEmpty()) {
            return null;
        }

        return [
            'batch' => $latestBatch,
            'total' => $deliveries->count(),
            'queued' => $deliveries->filter(function ($delivery) {
                return $delivery->deliveryStatus() === 'queued';
            })->count(),
            'sent' => $deliveries->filter(function ($delivery) {
                return $delivery->deliveryStatus() === 'sent';
            })->count(),
            'failed' => $deliveries->filter(function ($delivery) {
                return $delivery->deliveryStatus() === 'failed';
            })->count(),
            'recipients' => $deliveries->map(function ($delivery) {
                return [
                    'id' => $delivery->id,
                    'sacre' => optional($delivery->sacre)->title,
                    'email' => $delivery->recipient_email,
                    'status' => $delivery->deliveryStatus(),
                    'failure_message' => $delivery->failure_message,
                ];
            })->values(),
        ];
    }

    /**
     * Boot Function
     */
    public static function boot()
    {
        parent::boot();
        self::deleting(function ($invoice) { // before delete() method call this
            $invoice->invoices()->each(function ($sacreInvoice) {
                $sacreInvoice->delete();
            });
        });
    }


}
