<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentPasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    public User $student;
    public string $resetUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $student, string $resetUrl)
    {
        $this->student = $student;
        $this->resetUrl = $resetUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $from = null;
        if (auth()->check() && auth()->user()->isAdmin() && auth()->user()->email) {
            $from = new Address(auth()->user()->email, auth()->user()->nama_lengkap);
        }

        return new Envelope(
            from: $from,
            subject: 'Reset Password Akun SIMagang',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.student.password-reset',
        );
    }
}
