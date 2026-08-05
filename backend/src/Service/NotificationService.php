<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class NotificationService
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function sendEmail(string $to, string $subject, string $body): void
    {
        // In production, integrate with Symfony Mailer or external provider
        // This simulates async email sending via Messenger queue
        $this->logger->info('Notification sent', [
            'to' => $to,
            'subject' => $subject,
            'body' => $body,
        ]);
    }
}
