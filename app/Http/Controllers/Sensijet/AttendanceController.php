<?php

namespace App\Http\Controllers\Sensijet;

use App\Http\Controllers\Controller;
use App\Models\KbmAttendance;
use App\Models\KbmSession;
use App\Models\Student;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function storeForTeacher(Request $request, KbmSession $session)
    {
        $user = $request->user();

        if ($session->teacher_id && $session->teacher_id !== $user->id) {
            abort(403, 'Anda tidak berwenang mengisi absensi sesi ini.');
        }

        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'status'     => 'required|in:present,absent,sick,leave',
            'note'       => 'nullable|string',
        ]);

        $data['kbm_session_id'] = $session->id;

        KbmAttendance::updateOrCreate(
            [
                'kbm_session_id' => $session->id,
                'student_id' => $data['student_id'],
            ],
            [
                'status' => $data['status'],
                'note' => $data['note'] ?? null,
            ]
        );

        return back()->with('success', 'Absensi tersimpan.');
    }
}
