<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ClassRoom extends Model
{
    use HasFactory;

    // Ini yang dipakai:
    protected $table = 'classes';

    // JANGAN ada baris kedua yang override ke 'class_rooms'
    // hapus saja yang bawah kalau masih ada.

    protected $fillable = [
        'name',
        'level',
        'program_id',
        'teacher_id',
        'academic_period',
        'quota',
        'sessions_quota',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_active'  => 'bool',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function sessions()
    {
        return $this->hasMany(KbmSession::class, 'class_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'class_room_student')
            ->withPivot('status')
            ->withTimestamps();
    }
}
