<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Game;


class PlayerAnsweredQuestions extends Mailable
{
    use Queueable, SerializesModels;
    public $player;
    public $game;

    /**
     * Create a new message instance.
     */
    public function __construct(
        $player,
        $game
    ) {
        $this->player = $player;
        $this->game = $game;
    }


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: $this->player->first_name.' '.$this->player->last_name.' Just Completed Their Trivius Quiz!',
        );
    }


    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.player-complete',
            with: ['player' => $this->player,'game' => $this->game,'host' => $this->game->host,],
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
