<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KbmAttendance extends Model
{
    use HasFactory;

    protected $table = 'kbm_attendance';

    protected $fillable = [
        'kbm_session_id',
        'student_id',
        'status',
        'note',
    ];

    public function session()
    {
        return $this->belongsTo(KbmSession::class, 'kbm_session_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
