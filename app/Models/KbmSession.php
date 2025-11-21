<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KbmSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'teacher_id',
        'student_id',
        'date',
        'start_time',
        'end_time',
        'duration_minutes',
        'type',
        'is_counted_in_quota',
        'is_chargeable',
        'teacher_fee',
        'topic',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
        'is_counted_in_quota' => 'bool',
        'is_chargeable' => 'bool',
    ];

    protected static function booted()
    {
        static::saving(function (self $session) {
            if (! $session->duration_minutes && $session->start_time && $session->end_time) {
                $start = strtotime($session->start_time);
                $end = strtotime($session->end_time);
                if ($end > $start) {
                    $session->duration_minutes = (int) (($end - $start) / 60);
                }
            }

            if (! $session->teacher_fee && $session->duration_minutes) {
                $session->teacher_fee = $session->duration_minutes * 1000; // default 1000/menit
            }
        });
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function attendance()
    {
        return $this->hasMany(KbmAttendance::class, 'kbm_session_id');
    }

    public function charges()
    {
        return $this->hasMany(SessionCharge::class, 'kbm_session_id');
    }
}
