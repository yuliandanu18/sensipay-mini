<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_code',
        'student_id',
        'parent_user_id',          // ✅ ganti dari parent_id
        'program_id',
        'total_amount',
        'paid_amount',
        'status',
        'due_date',
        'last_reminder_sent_at',
    ];

    protected $casts = [          // ✅ WAJIB agar due_date jadi Carbon
        'due_date' => 'date',
        'last_reminder_sent_at' => 'datetime',
        'total_amount' => 'decimal:0',
        'paid_amount' => 'decimal:0',
    ];

    // public function parent()
    // {
    //     return $this->belongsTo(\App\Models\ParentModel::class, 'parent_user_id');
    //     // Catatan: kalau parent sebenarnya tabel users, nanti kita ganti ke User::class
    // }
public function parentUser()
{
    return $this->belongsTo(User::class, 'parent_user_id');
}

// alias kompatibel
public function parent()
{
    return $this->belongsTo(User::class, 'parent_user_id');
}

public function getParentWhatsappAttribute(): ?string
{
    return $this->parentUser?->whatsapp_number;
}

    public function student()
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id');
    }

    public function program()
    {
        return $this->belongsTo(\App\Models\Program::class, 'program_id');
    }

    public function paymentProofs()
    {
        return $this->hasMany(PaymentProof::class);
    }
}
