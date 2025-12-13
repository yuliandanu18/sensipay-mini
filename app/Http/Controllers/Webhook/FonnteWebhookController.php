<?php
namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WaReminder;
use Illuminate\Support\Facades\Log;

class FonnteWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Fonnte webhook received', ['payload'=>$request->all()]);

        $status = $request->input('status');
        $reference = $request->input('tag');
        $providerId = $request->input('id');

        if (!$reference) return response()->json(['ok'=>true,'msg'=>'no reference']);

        $log = WaReminder::where('reference',$reference)->first();
        if (!$log) return response()->json(['ok'=>true,'msg'=>'log not found']);

        $log->provider_message_id = $providerId ?: $log->provider_message_id;
        $log->last_payload = $request->all();

        if ($status==='delivered'){
            $log->status='delivered'; $log->delivered_at=now();
        } elseif ($status==='failed'){
            $log->status='failed'; $log->failed_at=now();
        } elseif ($status==='read'){
            $log->status='read';
        } else {
            $log->status = $status ?: $log->status;
        }

        $log->save();
        return response()->json(['ok'=>true]);
    }
}