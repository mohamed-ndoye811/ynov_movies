<?php

namespace App\Message;

use App\Entity\Reservation;

class ReservationMailNotification
{
    public function __construct(
        private Reservation $reservation,
    ) {
    }

    public function getContent(): string
    {
        return $this->reservation->getUid();
    }
}