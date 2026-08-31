<?php

namespace App\Http\View\Composers;

use App\Models\Batch;
use App\Traits\ManagesFeedingSchedules;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class NotificationComposer
{
    use ManagesFeedingSchedules;

    public function compose(View $view)
    {
        if (auth()->check()) {
            $user = auth()->user();
            $batches = Batch::where('status', 'active')->get();
            $upcomingFeedings = $this->getUpcomingFeedingsForBatches($batches);

            $dismissed = DB::table('notification_dismissals')
                ->where('user_id', $user->id)
                ->get()
                ->keyBy(function ($item) {
                    return $item->batch_id . '-' . $item->schedule_time;
                });

            $notifications = $upcomingFeedings->filter(function ($schedule) use ($dismissed) {
                $isActionable = in_array($schedule->status, ['due', 'missed']);
                $isDismissed = $dismissed->has($schedule->batch_id . '-' . $schedule->schedule_time);
                return $isActionable && !$isDismissed;
            });

            $view->with('notifications', $notifications);
        }
    }
}