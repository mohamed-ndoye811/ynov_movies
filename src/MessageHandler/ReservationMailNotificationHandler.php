<?php
// src/MessageHandler/SmsNotificationHandler.php
namespace App\MessageHandler;

use App\Message\ReservationMailNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ReservationMailNotificationHandler
{
    public function __invoke(ReservationMailNotification $message)
    {
        return $message->getContent();
    }
}