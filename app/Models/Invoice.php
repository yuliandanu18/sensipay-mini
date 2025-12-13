<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_code',
        'student_id',
        'parent_id',
        'program_id',
        'total_amount',
        'paid_amount',
        'status',
        'due_date',
        'last_reminder_sent_at',
    ];

    public function parent()
    {
       
         return $this->belongsTo(\App\Models\ParentModel::class, 'parent_user_id');

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
