<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProjectSummary extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public readonly Project $project;
    public readonly float   $rebated;
    public readonly float   $total;
    public readonly array   $chart;

    /**
     * Create a new message instance.
     */
    public function __construct(
        array $summary
    ) {
        $this->project = $summary['project'];
        $this->rebated = $summary['rebated'];
        $this->total   = $summary['total'];
        $this->chart   = $summary['chart'];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Project {$this->project->name} Summary",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'ProjectSummary',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
