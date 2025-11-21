<?php
namespace App\Http\Controllers\Sensijet;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\KbmSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Sensijet\OwnerDashboardController;

class OwnerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $start = now()->startOfMonth();
        $end   = now()->endOfMonth();

        // pemasukan bulan ini (pakai created_at)
$income = Payment::whereBetween('created_at', [$start, $end])->sum('amount');

        $piutang = Invoice::where('status','unpaid')->sum(DB::raw('total_amount - paid_amount'));

        $sessions = KbmSession::whereBetween('date', [$start->toDateString(), $end->toDateString()])->count();

        $payroll = KbmSession::whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->sum('teacher_fee');

       $days = [];
for ($i=29; $i>=0; $i--) {
    $day = now()->subDays($i)->toDateString();
    $days[$day] = [
        'date' => $day,
        'income' => Payment::whereDate('created_at',$day)->sum('amount'),
        'sessions' => KbmSession::whereDate('date',$day)->count(),
    ];
}

        $top_unpaid = Invoice::where('status','unpaid')
            ->orderBy('total_amount','desc')
            ->limit(10)
            ->get();

        $top_teachers = KbmSession::select('teacher_id', DB::raw('count(*) as total'))
            ->whereBetween('date', [$start->toDateString(),$end->toDateString()])
            ->groupBy('teacher_id')
            ->orderBy('total','desc')
            ->limit(10)
            ->with('teacher')
            ->get();

        return view('sensijet.dashboard.owner',[
            'income'=>$income,
            'piutang'=>$piutang,
            'sessions'=>$sessions,
            'payroll'=>$payroll,
            'chart'=>$days,
            'top_unpaid'=>$top_unpaid,
            'top_teachers'=>$top_teachers,
            'start'=>$start,
            'end'=>$end
        ]);
    }
}
