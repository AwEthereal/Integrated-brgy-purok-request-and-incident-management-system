<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\IncidentReport;

class IncidentReportStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $report;
    protected $oldStatus;
    protected $newStatus;

    /**
     * Create a new notification instance.
     *
     * @param IncidentReport $report
     * @param string $oldStatus
     * @param string $newStatus
     */
    public function __construct(IncidentReport $report, $oldStatus, $newStatus)
    {
        $this->report = $report;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
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
        $incidentType = IncidentReport::TYPES[$this->report->incident_type] ?? 'Incident';
        $statusLabels = IncidentReport::getStatuses();
        $newStatusLabel = $statusLabels[$this->newStatus] ?? ucfirst(str_replace('_', ' ', $this->newStatus));
        
        $mailMessage = (new MailMessage)
            ->subject('Incident Report Update - ' . config('app.name'))
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your incident report has been updated.')
            ->line('**Report Details:**')
            ->line('Report ID: #' . $this->report->id)
            ->line('Incident Type: ' . $incidentType)
            ->line('Status: ' . $newStatusLabel)
            ->line('Reported Date: ' . $this->report->created_at->format('F d, Y h:i A'));

        // Add location if available
        if ($this->report->location) {
            $mailMessage->line('Location: ' . $this->report->location);
        }

        // Status-specific messages
        if ($this->newStatus === 'in_progress') {
            $mailMessage->line('**Update:**')
                ->line('Our team is now actively working on resolving this incident. We will keep you informed of any developments.');
        } elseif ($this->newStatus === 'resolved' || $this->newStatus === 'approved') {
            $mailMessage->line('**Great News!**')
                ->line('This incident has been resolved. Thank you for reporting and helping make our community safer.')
                ->line('We would appreciate your feedback on how we handled this incident.')
                ->action('Provide Feedback', url('/incident-reports/' . $this->report->id));
        } elseif ($this->newStatus === 'rejected' || $this->newStatus === 'invalid') {
            $mailMessage->line('**Status Update:**')
                ->line('After review, this report has been marked as invalid or rejected.');
            
            // Add rejection reason if available
            if ($this->report->rejection_reason) {
                $mailMessage->line('**Reason for Rejection:**')
                    ->line($this->report->rejection_reason);
            }
        }

        // Add staff notes if available (for non-rejected statuses, or if there are additional notes)
        if ($this->report->staff_notes) {
            $mailMessage->line('**Additional Notes from Staff:**')
                ->line($this->report->staff_notes);
        }

        $mailMessage->action('View Report Details', url('/incident-reports/' . $this->report->id))
            ->line('Thank you for your vigilance in keeping our community safe.')
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
            'report_id' => $this->report->id,
            'incident_type' => $this->report->incident_type,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ];
    }
}
