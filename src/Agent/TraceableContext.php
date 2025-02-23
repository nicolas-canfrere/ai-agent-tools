<?php

declare(strict_types=1);

namespace App\Agent;

use Psr\Log\LoggerInterface;

class TraceableContext implements ContextInterface
{
    public function __construct(
        private readonly ContextInterface $decorated,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function append(Message $message): void
    {
        $this->decorated->append($message);
        $this->logger->debug(
            'Message appended',
            ['message' => $message]
        );
    }

    public function isEmpty(): bool
    {
        return $this->decorated->isEmpty();
    }

    /**
     * {@inheritDoc}
     */
    public function getMessages(): array
    {
        return $this->decorated->getMessages();
    }
}
