<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitePlayer extends Mailable
{
    use Queueable, SerializesModels;

    public $invitee;

    public $game;

    /**
     * Create a new message instance.
     */
    public function __construct(
        $invitee,
        $game
    ) {
        $this->invitee = $invitee;
        $this->game = $game;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: $this->game->host->first_name.' '.$this->game->host->last_name.' has invited you to a party',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invitation',
            with: ['invitee' => $this->invitee, 'game' => $this->game, 'host' => $this->game->host],
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
