<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'level',
        'price_per_session',
        'total_sessions',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function getTotalPackagePriceAttribute(): float
    {
        return (float) ($this->price_per_session * $this->total_sessions);
    }
}
