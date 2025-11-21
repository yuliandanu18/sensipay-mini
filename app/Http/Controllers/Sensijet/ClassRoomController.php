<?php

namespace App\Http\Controllers\Sensijet;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;

class ClassRoomController extends Controller
{
    public function index()
    {
        $classes = ClassRoom::with(['program', 'teacher'])
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->paginate(20);

        return view('sensijet.classes.index', compact('classes'));
    }

    public function create()
    {
        $programs = Program::orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();

        return view('sensijet.classes.create', compact('programs', 'teachers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'level'           => 'nullable|string|max:50',
            'program_id'      => 'nullable|exists:programs,id',
            'teacher_id'      => 'nullable|exists:users,id',
            'sessions_quota'  => 'required|integer|min:1',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'is_active'       => 'sometimes|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $classRoom = ClassRoom::create($data);

        return redirect()
            ->route('sensijet.classes.show', $classRoom)
            ->with('success', 'Kelas berhasil dibuat.');
    }

    public function show(ClassRoom $class)
    {
        $class->load(['program', 'teacher', 'sessions' => function ($q) {
            $q->orderBy('date')->orderBy('start_time');
        }]);

        return view('sensijet.classes.show', [
            'classRoom' => $class,
        ]);
    }

    public function edit(ClassRoom $class)
    {
        $programs = Program::orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();

        return view('sensijet.classes.edit', [
            'classRoom' => $class,
            'programs' => $programs,
            'teachers' => $teachers,
        ]);
    }

    public function update(Request $request, ClassRoom $class)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'level'           => 'nullable|string|max:50',
            'program_id'      => 'nullable|exists:programs,id',
            'teacher_id'      => 'nullable|exists:users,id',
            'sessions_quota'  => 'required|integer|min:1',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'is_active'       => 'sometimes|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $class->update($data);

        return redirect()
            ->route('sensijet.classes.show', $class)
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(ClassRoom $class)
    {
        $class->delete();

        return redirect()
            ->route('sensijet.classes.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }
}
