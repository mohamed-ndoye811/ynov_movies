<?php
// src/MessageHandler/SmsNotificationHandler.php
namespace App\MessageHandler;

use App\Message\TestNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TestNotificationHandler
{
    public function __invoke(TestNotification $message)
    {
        return $message->getContent();
    }
}