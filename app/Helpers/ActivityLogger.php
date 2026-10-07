<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public static function logLogin($user)
    {
        return ActivityLog::create([
            'user_id' => $user->id,
            'activity_type' => 'login',
            'description' => 'User logged in to the system',
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'login_at' => now(),
            'session_id' => session()->getId(),
        ]);
    }

    public static function logLogout($user)
    {
        // Find the active session for this user and update logout time
        $activeSession = ActivityLog::where('user_id', $user->id)
            ->whereNull('logout_at')
            ->where('activity_type', 'login')
            ->latest()
            ->first();

        if ($activeSession) {
            $activeSession->update([
                'logout_at' => now(),
                'description' => $activeSession->description . ' | Logged out',
            ]);
        }

        return ActivityLog::create([
            'user_id' => $user->id,
            'activity_type' => 'logout',
            'description' => 'User logged out of the system',
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'session_id' => session()->getId(),
        ]);
    }

    public static function logActivity($user, $type, $description = null)
    {
        return ActivityLog::create([
            'user_id' => $user->id,
            'activity_type' => $type,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'session_id' => session()->getId(),
        ]);
    }
}