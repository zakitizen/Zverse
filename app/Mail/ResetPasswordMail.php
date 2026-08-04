<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email notifikasi reset password Zverse.
 *
 * Dikirim ke user yang meminta reset password (liat alur reset di
 * AuthController). Mailable ini mendukung queue (Queueable) agar pengiriman
 * email tidak memblokir response.
 */
class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /** Token verifikasi untuk link reset. */
    public string $token;

    /** Alamat email penerima. */
    public string $email;

    /**
     * Membuat instance mailable baru.
     *
     * @param string $token Token reset password.
     * @param string $email Email penerima.
     */
    public function __construct(string $token, string $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    /**
     * Mendefinisikan amplop (pengirim/penerima/subjek) email.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Password — Zverse',
        );
    }

    /**
     * Menentukan isi (view) email.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reset-password',
        );
    }
}
