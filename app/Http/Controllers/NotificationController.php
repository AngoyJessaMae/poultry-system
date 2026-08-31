<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function dismiss(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|integer',
            'schedule_time' => 'required|string',
        ]);

        DB::table('notification_dismissals')->updateOrInsert(
            [
                'user_id' => auth()->id(),
                'batch_id' => $request->batch_id,
                'schedule_time' => $request->schedule_time,
            ],
            ['created_at' => now(), 'updated_at' => now()]
        );

        return back()->with('status', 'Notification dismissed.');
    }
}