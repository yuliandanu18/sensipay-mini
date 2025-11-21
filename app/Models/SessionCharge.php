<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionCharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'kbm_session_id',
        'invoice_id',
        'amount',
        'description',
        'approved_by_academic',
        'approved_by_operational',
    ];

    protected $casts = [
        'approved_by_academic' => 'bool',
        'approved_by_operational' => 'bool',
    ];

    public function session()
    {
        return $this->belongsTo(KbmSession::class, 'kbm_session_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
