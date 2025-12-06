<?php

namespace App\Mail;

use App\Models\BudgetRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BudgetRequestApprovedAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public BudgetRequest $budgetRequest)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Budget Request Fully Approved by Admin - {$this->budgetRequest->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.budget-request-approved-admin',
            with: [
                'budgetRequest' => $this->budgetRequest,
                'userName' => $this->budgetRequest->user->name,
                'departmentName' => $this->budgetRequest->department->name,
                'amount' => $this->budgetRequest->amount,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
