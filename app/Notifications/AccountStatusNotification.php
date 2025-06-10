<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class AccountStatusNotification extends Notification
{
    use Queueable;

    /**
     * The status of the account (approved/rejected).
     *
     * @var string
     */
    public $status;

    /**
     * The rejection reason, if any.
     *
     * @var string|null
     */
    public $reason;

    /**
     * Create a new notification instance.
     *
     * @param string $status
     * @param string|null $reason
     * @return void
     */
    public function __construct($status, $reason = null)
    {
        $this->status = $status;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject('Account ' . ucfirst($this->status) . ' - ' . config('app.name'))
            ->greeting('Hello ' . $notifiable->name . ',');

        if ($this->status === 'approved') {
            $mailMessage->line('We are pleased to inform you that your account has been approved.')
                ->line('You can now log in to your account and access all the features.')
                ->action('Login to Your Account', url('/login'))
                ->line('Thank you for registering with us!');
        } else {
            $mailMessage->line('We regret to inform you that your account has been rejected.');
            
            if ($this->reason) {
                $mailMessage->line('Reason for rejection:')
                    ->line(new HtmlString('<strong>' . e($this->reason) . '</strong>'));
            }
            
            $mailMessage->line('If you believe this is a mistake, please contact our support team.');
        }

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'status' => $this->status,
            'reason' => $this->reason,
        ];
    }
}
