<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Request as RequestModel;

class RequestRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $request;
    protected $rejectionType;

    /**
     * Create a new notification instance.
     *
     * @param RequestModel $request
     * @param string $rejectionType ('purok' or 'barangay')
     */
    public function __construct(RequestModel $request, $rejectionType = 'barangay')
    {
        $this->request = $request;
        $this->rejectionType = $rejectionType;
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
        $formType = RequestModel::FORM_TYPES[$this->request->form_type] ?? 'Document';
        $rejectedBy = $this->rejectionType === 'purok' ? 'Purok Leader' : 'Barangay Office';
        
        $mailMessage = (new MailMessage)
            ->subject('Purok Clearance Request Update - Action Required - ' . config('app.name'))
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We regret to inform you that your Purok Clearance request has not been approved by the ' . $rejectedBy . '.')
            ->line('**Request Details:**')
            ->line('Request ID: #' . $this->request->id)
            ->line('Document Type: ' . $formType)
            ->line('Purpose: ' . $this->request->purpose)
            ->line('Rejected Date: ' . $this->request->rejected_at->format('F d, Y h:i A'));

        // Add rejection reason if available
        if ($this->request->rejection_reason) {
            $mailMessage->line('**Reason for Rejection:**')
                ->line($this->request->rejection_reason);
        }

        $mailMessage->line('**What You Can Do:**')
            ->line('Review the rejection reason carefully and send a new request with the correct information')
            ->action('Submit New Request', url('/requests/create'))
            ->line('If you believe this rejection was made in error or need clarification, please contact your purok president.')
            ->line('We apologize for any inconvenience.')
            ->line('- Barangay Kalawag Dos');

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
            'request_id' => $this->request->id,
            'form_type' => $this->request->form_type,
            'rejection_type' => $this->rejectionType,
            'rejection_reason' => $this->request->rejection_reason,
        ];
    }
}
