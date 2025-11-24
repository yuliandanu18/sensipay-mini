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
        'parent_user_id',
        'program_id',
        'total_amount',
        'paid_amount',
        'status',
        'due_date',
        'last_reminder_sent_at', // optional, biar bisa mass-assign kalau perlu
    ];

    protected $casts = [
        'due_date'             => 'date',
        'total_amount'         => 'float',
        'paid_amount'          => 'float',
        'last_reminder_sent_at'=> 'datetime',
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
        return $this->status === 'paid'
            || ($this->paid_amount ?? 0) >= ($this->total_amount ?? 0);
    }

    public function getRemainingAttribute(): float
    {
        return max(0, ($this->total_amount ?? 0) - ($this->paid_amount ?? 0));
    }

    public function recalcStatus(): void
    {
        $total = $this->total_amount ?? 0;
        $paid  = $this->paid_amount ?? 0;

        if ($paid <= 0) {
            $this->status = 'unpaid';
        } elseif ($paid > 0 && $paid < $total) {
            $this->status = 'partial';
        } else {
            $this->status = 'paid';
        }

        $this->save();
    }

    /**
     * Bantu cek apakah perlu dikirimi reminder hari ini.
     */
    public function shouldSendReminderToday(): bool
    {
        if (! $this->due_date) {
            return false;
        }

        $today    = now()->startOfDay();
        $nextWeek = now()->addDays(7)->endOfDay();

        $status = $this->status;
        $isUnpaid = in_array($status, ['unpaid', 'partial', null], true);

        if (! $isUnpaid) {
            return false;
        }

        // Jatuh tempo dalam 7 hari ke depan atau sudah lewat
        if (! $this->due_date->between($today, $nextWeek) && ! $this->due_date->lt($today)) {
            return false;
        }

        // Jangan kirim dua kali di hari yang sama
        if ($this->last_reminder_sent_at && $this->last_reminder_sent_at->greaterThanOrEqualTo($today)) {
            return false;
        }

        return true;
    }
}
