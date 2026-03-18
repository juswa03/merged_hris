<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    public function __construct(
        public readonly string  $title,
        public readonly string  $message,
        public readonly string  $type = 'info',   // info | success | warning | danger
        public readonly ?string $link = null,
    ) {}

    public function via(mixed $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(mixed $notifiable): array
    {
        return [
            'title'   => $this->title,
            'message' => $this->message,
            'type'    => $this->type,
            'link'    => $this->link,
        ];
    }
}
