<?php

namespace App\MessageHandler;

use App\Message\SendNotificationMessage;
use App\Service\NotificationService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendNotificationHandler
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {
    }

    public function __invoke(SendNotificationMessage $message): void
    {
        $this->notificationService->sendEmail(
            $message->getRecipient(),
            $message->getSubject(),
            $message->getBody()
        );
    }
}
