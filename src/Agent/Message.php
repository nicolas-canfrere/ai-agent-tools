<?php

declare(strict_types=1);

namespace App\Agent;

class Message implements \JsonSerializable
{
    public function __construct(
        public readonly RolesEnum $role,
        public readonly string $content,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'role' => $this->role->value,
            'content' => $this->content,
        ];
    }
}
