<?php

namespace App\Http\Controllers\Sensijet;

use App\Http\Controllers\Controller;
use App\Models\KbmSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;


class PayrollController extends Controller
{
    public function index(Request $request)
    {
        // Default: bulan berjalan
        $month = $request->input('month');
        if ($month) {
            // format: YYYY-MM
            [$year, $mon] = explode('-', $month);
            $start = Carbon::createFromDate((int) $year, (int) $mon, 1)->startOfDay();
            $end   = (clone $start)->endOfMonth();
        } else {
            $start = now()->startOfMonth();
            $end   = now()->endOfMonth();
            $month = $start->format('Y-m');
        }

        $sessions = KbmSession::with('teacher')
            ->whereNotNull('teacher_id')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date')
            ->get();

        $byTeacher = [];

        foreach ($sessions as $session) {
            if (! $session->teacher) {
                continue;
            }

            $tid = $session->teacher_id;

            if (! isset($byTeacher[$tid])) {
                $byTeacher[$tid] = [
                    'teacher'          => $session->teacher,
                    'total_sessions'   => 0,
                    'regular_sessions' => 0,
                    'private_sessions' => 0,
                    'other_sessions'   => 0,
                    'total_minutes'    => 0,
                    'total_fee'        => 0,
                ];
            }

            $byTeacher[$tid]['total_sessions']++;
            $byTeacher[$tid]['total_minutes'] += (int) $session->duration_minutes;
            $byTeacher[$tid]['total_fee'] += (float) $session->teacher_fee;

            if ($session->type === 'regular') {
                $byTeacher[$tid]['regular_sessions']++;
            } elseif ($session->type === 'private') {
                $byTeacher[$tid]['private_sessions']++;
            } else {
                $byTeacher[$tid]['other_sessions']++;
            }
        }

        // sort by teacher name
        uasort($byTeacher, function ($a, $b) {
            return strcmp($a['teacher']->name ?? '', $b['teacher']->name ?? '');
        });

        return view('sensijet.payroll.index', [
            'month'      => $month,
            'start'      => $start,
            'end'        => $end,
            'summaries'  => $byTeacher,
        ]);
    }

    public function show(Request $request, User $teacher)
    {
        $month = $request->input('month');
        if ($month) {
            [$year, $mon] = explode('-', $month);
            $start = Carbon::createFromDate((int) $year, (int) $mon, 1)->startOfDay();
            $end   = (clone $start)->endOfMonth();
        } else {
            $start = now()->startOfMonth();
            $end   = now()->endOfMonth();
            $month = $start->format('Y-m');
        }

        $sessions = KbmSession::where('teacher_id', $teacher->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        $summary = [
            'total_sessions'   => $sessions->count(),
            'regular_sessions' => $sessions->where('type', 'regular')->count(),
            'private_sessions' => $sessions->where('type', 'private')->count(),
            'other_sessions'   => $sessions->whereNotIn('type', ['regular', 'private'])->count(),
            'total_minutes'    => (int) $sessions->sum('duration_minutes'),
            'total_fee'        => (float) $sessions->sum('teacher_fee'),
        ];

        return view('sensijet.payroll.show', [
            'month'    => $month,
            'start'    => $start,
            'end'      => $end,
            'teacher'  => $teacher,
            'summary'  => $summary,
            'sessions' => $sessions,
        ]);
    }
}
