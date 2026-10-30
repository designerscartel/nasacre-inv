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
        'batch' => 'integer',
    ];

    /**
     * Return a Resource
     */
    public function toResource()
    {
        return new \App\Http\Resources\InvoiceResource($this);
    }

    public function invoices()
    {
        return $this->hasMany(SacreInvoice::class);
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
