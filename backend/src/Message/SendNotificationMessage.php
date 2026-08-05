<?php

namespace App\Message;

class SendNotificationMessage
{
    public function __construct(
        private readonly string $recipient,
        private readonly string $subject,
        private readonly string $body,
    ) {
    }

    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
