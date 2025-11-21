<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

protected static function booted(): void
{
    static::created(function (Payment $payment) {
        if ($payment->invoice) {
            $invoice = $payment->invoice;
            $invoice->paid_amount = $invoice->payments()->sum('amount');
            $invoice->recalcStatus();
        }
    });
}

    protected $fillable = [
        'invoice_id',
        'amount',
        'paid_at',
        'method',
        'note',
    ];

    protected $casts = [
        'paid_at' => 'date',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
