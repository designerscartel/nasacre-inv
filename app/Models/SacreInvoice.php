<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SacreInvoice extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'po_number',
        'address',
        'date',
        'sacre_id',
        'invoice_id',
        'batch',
        'delivery_status',
        'recipient_name',
        'recipient_email',
        'queued_at',
        'sent_at',
        'failed_at',
        'failure_message',
        'virtual_training',
        'total_override',
    ];

    protected $casts = [
        'date' => 'datetime',
        'queued_at' => 'datetime',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
        'batch' => 'integer',
        'virtual_training' => 'boolean',
        'total_override' => 'decimal:2',
    ];

    /**
     * Get the sacre it to.
     */
    public function sacre()
    {
        return $this->belongsTo(Sacre::class, 'sacre_id');
    }

    /**
     * Get the invoice it to.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function totalAmount(): float
    {
        if ($this->total_override !== null) {
            return (float) $this->total_override;
        }

        $baseAmount = $this->invoice ? (float) $this->invoice->subs : 0;

        return $baseAmount + ($this->virtual_training ? 60.0 : 0.0);
    }

    public function deliveryStatus(): string
    {
        return $this->delivery_status ?: 'sent';
    }

    /**
     * Return a Resource
     */
    public function toResource()
    {
        return new \App\Http\Resources\SacreInvoiceResource($this);
    }
}
