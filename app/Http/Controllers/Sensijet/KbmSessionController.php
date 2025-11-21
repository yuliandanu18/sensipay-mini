<?php

namespace App\Http\Controllers\Sensijet;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\KbmSession;
use Illuminate\Http\Request;

class KbmSessionController extends Controller
{
    // FORM TAMBAH SESI UNTUK SATU KELAS
    public function createForClass(ClassRoom $classRoom)
    {
        // Siswa aktif di kelas ini
        $students = $classRoom->students()
            ->wherePivot('status', 'active')
            ->orderBy('name')
            ->get();

        $teacher = $classRoom->teacher;

        return view('sensijet.sessions.create', [
            'classRoom' => $classRoom,
            'students'  => $students,
            'teacher'   => $teacher,
        ]);
    }

    // SIMPAN SESI UNTUK SATU KELAS
    public function storeForClass(Request $request, ClassRoom $classRoom)
    {
        $data = $request->validate([
            'date'       => ['required', 'date'],
            'start_time' => ['required'],
            'end_time'   => ['required'],
            'topic'      => ['nullable', 'string', 'max:255'],
            'teacher_id' => ['nullable', 'integer'],
        ]);

        $teacherId = $data['teacher_id'] ?? $classRoom->teacher_id;

        KbmSession::create([
            'class_id'   => $classRoom->id,   // GANTI kalau FK di DB-mu 'class_room_id'
            'teacher_id' => $teacherId,
            'date'       => $data['date'],
            'start_time' => $data['start_time'],
            'end_time'   => $data['end_time'],
            'topic'      => $data['topic'] ?? null,
        ]);

        return redirect()
            ->route('sensijet.classes.show', $classRoom)
            ->with('success', 'Sesi KBM berhasil ditambahkan.');
    }
}
