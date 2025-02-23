<?php

declare(strict_types=1);

namespace App\Agent;

interface ContextInterface
{
    public function append(Message $message): void;

    public function isEmpty(): bool;

    /**
     * @return array<int, Message>
     */
    public function getMessages(): array;
}
