<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_code',
        'student_id',
        'program_id',
        'total_amount',
        'paid_amount',
        'status',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid' || $this->paid_amount >= $this->total_amount;
    }

    public function getRemainingAttribute(): float
    {
        return max(0, ($this->total_amount ?? 0) - ($this->paid_amount ?? 0));
    }
}
