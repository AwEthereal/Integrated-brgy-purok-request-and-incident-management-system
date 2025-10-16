<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Request as RequestModel;

class RequestApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $request;
    protected $approvalType;

    /**
     * Create a new notification instance.
     *
     * @param RequestModel $request
     * @param string $approvalType ('purok' or 'barangay')
     */
    public function __construct(RequestModel $request, $approvalType = 'barangay')
    {
        $this->request = $request;
        $this->approvalType = $approvalType;
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
        
        if ($this->approvalType === 'purok') {
            return (new MailMessage)
                ->subject('Request Approved by Purok Leader - ' . config('app.name'))
                ->greeting('Hello ' . $notifiable->name . ',')
                ->line('Great news! Your Purok clearance request has been approved by your Purok Leader.')
                ->line('**Request Details:**')
                ->line('Request ID: #' . $this->request->id)
                ->line('Document Type: ' . $formType)
                ->line('Purpose: ' . $this->request->purpose)
                ->line('Approved Date: ' . $this->request->purok_approved_at->format('F d, Y h:i A'))
                ->line('**Next Steps:**')
                ->line('Your Purok Clearance is ready for pick up. Review your online request for any instructions given by your purok president.')
                ->action('View Request Details', url('/my-requests'))
                ->line('Thank you for using our system!');
        } else {
            // Barangay approval
            return (new MailMessage)
                ->subject('Request Approved - Ready for Pickup! - ' . config('app.name'))
                ->greeting('Hello ' . $notifiable->name . ',')
                ->line('Congratulations! Your ' . $formType . ' request has been approved by the Barangay Office.')
                ->line('**Request Details:**')
                ->line('Request ID: #' . $this->request->id)
                ->line('Document Type: ' . $formType)
                ->line('Purpose: ' . $this->request->purpose)
                ->line('Approved Date: ' . $this->request->barangay_approved_at->format('F d, Y h:i A'))
                ->line('**How to Claim Your Document:**')
                ->line('1. Visit the Barangay Office during office hours (8:00 AM - 5:00 PM, Monday to Friday)')
                ->line('2. Bring a valid ID for verification and your required documents')
                ->line('3. Present your required purok clearance' . $this->request->id)
                ->action('View Request Details', url('/my-requests'))
                ->line('Thank you for your patience!')
                ->line('- Barangay Kalawag Dos');
        }
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
            'approval_type' => $this->approvalType,
        ];
    }
}
