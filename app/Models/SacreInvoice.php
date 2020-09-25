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
        'sacre_id',
        'invoice_id',
        'batch'
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

    /**
     * Return a Resource
     */
    public function toResource()
    {
        return new \App\Http\Resources\ScareInvoiceResource($this);
    }
}
