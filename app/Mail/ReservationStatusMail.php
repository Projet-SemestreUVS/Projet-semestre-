<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $user;

    public function __construct(Reservation $reservation, $user)
    {
        $this->reservation = $reservation;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Mise à jour réservation KAY JOB')
                    ->view('emails.reservation-status');
    }
}