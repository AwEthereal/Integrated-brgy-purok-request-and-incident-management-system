<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\IncidentReport;

class PublicIncidentReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public IncidentReport $incident)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $app = config('app.name');
        return (new MailMessage)
            ->subject('We received your incident report')
            ->greeting('Thank you!')
            ->line('Your report has been received by '.$app.'.')
            ->line('We will review it as soon as possible and contact you if needed.')
            ->salutation('— '.$app);
    }
}
