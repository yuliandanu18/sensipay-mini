<?php

// app/Models/PaymentProof.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentProof extends Model
{
    protected $fillable = [
        'invoice_id',
        'uploaded_by',
        'file_path',
        'amount',
        'transfer_date',
        'bank_name',
        'status',
        'verified_by',
        'verified_at',
        'rejection_reason',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'verified_at'   => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
