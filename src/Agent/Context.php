<?php

declare(strict_types=1);

namespace App\Agent;

class Context implements ContextInterface
{
    /**
     * @var array<Message>
     */
    private array $messages = [];

    public function append(Message $message): void
    {
        $this->messages[] = $message;
    }

    /**
     * @return array<int, Message>
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    public function isEmpty(): bool
    {
        return empty($this->messages);
    }
}
