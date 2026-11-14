<?php

namespace App\Notifications;

use App\Models\ManagementRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class ManagementRequestSubmitted extends Notification
{
    use Queueable;

    public function __construct(private readonly ManagementRequest $managementRequest)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $label = ucfirst($this->managementRequest->request_type);

        return [
            'management_request_id' => $this->managementRequest->id,
            'message' => "New {$label} request requires approval.",
            'request_type' => $this->managementRequest->request_type,
        ];
    }
}
