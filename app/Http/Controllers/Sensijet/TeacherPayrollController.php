<?php

namespace App\Http\Controllers\Sensijet;

use App\Http\Controllers\Controller;
use App\Models\KbmSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TeacherPayrollController extends Controller
{
    public function index(Request $request)
    {
        // Format bulan: 2025-11
        $monthInput = $request->input('month');
        if (! $monthInput) {
            $monthInput = now()->format('Y-m');
        }

        try {
            [$year, $month] = explode('-', $monthInput);
            $start = Carbon::createFromDate((int) $year, (int) $month, 1)->startOfDay();
            $end   = (clone $start)->endOfMonth()->endOfDay();
        } catch (\Throwable $e) {
            $start = now()->startOfMonth();
            $end   = now()->endOfMonth();
            $monthInput = $start->format('Y-m');
        }

        $sessions = KbmSession::with('teacher')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->whereNotNull('teacher_id')
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        // Grup per guru
        $grouped = $sessions->groupBy('teacher_id');

        $rows = [];
        $totalAll = [
            'sessions' => 0,
            'minutes' => 0,
            'fee' => 0,
        ];

        foreach ($grouped as $teacherId => $teacherSessions) {
            /** @var \App\Models\User|null $teacher */
            $teacher = $teacherSessions->first()->teacher;

            $totalSessions = $teacherSessions->count();
            $totalMinutes = (int) $teacherSessions->sum('duration_minutes');
            $totalFee = (float) $teacherSessions->sum('teacher_fee');

            $regular = $teacherSessions->where('type', 'regular');
            $private = $teacherSessions->where('type', 'private');

            $rows[] = [
                'teacher' => $teacher,
                'total_sessions' => $totalSessions,
                'total_minutes' => $totalMinutes,
                'total_fee' => $totalFee,
                'regular_sessions' => $regular->count(),
                'regular_minutes' => (int) $regular->sum('duration_minutes'),
                'private_sessions' => $private->count(),
                'private_minutes' => (int) $private->sum('duration_minutes'),
            ];

            $totalAll['sessions'] += $totalSessions;
            $totalAll['minutes'] += $totalMinutes;
            $totalAll['fee']     += $totalFee;
        }

        // Ambil daftar guru (opsional untuk filter / tampilan)
        $teachers = User::where('role', 'teacher')
            ->orderBy('name')
            ->get();

        return view('sensijet.payroll.index', [
            'month' => $monthInput,
            'start' => $start,
            'end' => $end,
            'rows' => $rows,
            'totals' => $totalAll,
            'teachers' => $teachers,
        ]);
    }
}
