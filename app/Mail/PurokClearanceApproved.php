<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PurokClearanceApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $request;
    public $approver;

    /**
     * Create a new message instance.
     */
    public function __construct(Request $request, $approver)
    {
        $this->request = $request;
        $this->approver = $approver;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Purok Clearance Request Approved - Barangay Kalawag II',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.clearance-approved',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $email = $this->request->email ?? optional($this->request->user)->email;
        if (!is_string($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [];
        }

        if (!in_array($this->request->status, ['purok_approved', 'completed'], true)) {
            return [];
        }

        $req = $this->request->load(['purok', 'purokLeader']);

        $issueDate = now()->format('Y-m-d');
        if ($req->purok_approved_at) {
            try {
                $issueDate = $req->purok_approved_at->format('Y-m-d');
            } catch (\Throwable $e) {
            }
        }

        $age = null;
        if (!empty($req->purok_private_notes)) {
            try {
                $priv = json_decode($req->purok_private_notes, true);
                if (is_array($priv) && isset($priv['age']) && is_numeric($priv['age'])) {
                    $age = (int) $priv['age'];
                }
            } catch (\Throwable $e) {
            }
        }
        if (is_null($age) && $req->birth_date) {
            $age = now()->diffInYears($req->birth_date);
        }

        $pdf = Pdf::loadView('pdf.purok_clearance', [
            'req' => $req,
            'issue_date' => $issueDate,
            'age' => $age,
        ])->setPaper('A4');

        return [
            Attachment::fromData(fn () => $pdf->output(), 'purok-clearance-'.$req->id.'.pdf')
                ->withMime('application/pdf')
        ];
    }
}
