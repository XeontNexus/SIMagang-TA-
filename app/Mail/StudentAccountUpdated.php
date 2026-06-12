<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentAccountUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public User $student;
    public string $newPassword;
    public string $loginUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $student, string $newPassword)
    {
        $this->student = $student;
        $this->newPassword = $newPassword;
        $this->loginUrl = url('/login');
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
            subject: 'Akun PKL SIMagang Anda Telah Diperbarui',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.student.account-updated',
        );
    }
}
