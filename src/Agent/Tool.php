<?php

declare(strict_types=1);

namespace App\Agent;

class Tool implements \JsonSerializable
{
    public function __construct(
        private readonly string $name,
        private readonly string $description,
        private readonly mixed $callback,
        private readonly ?string $parametersAsJsonSchema = null,
    ) {
        if (!is_callable($this->callback)) {
            throw new \InvalidArgumentException('The callback must be a callable');
        }
    }

    /**
     * @param array<int|string, mixed>|null $parameters
     */
    public function execute(?array $parameters = []): mixed
    {
        return call_user_func_array($this->callback, $parameters);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $function = [
            'name' => $this->name,
            'description' => $this->description,
        ];
        if (null !== $this->parametersAsJsonSchema) {
            $function['parameters'] = $this->parametersAsJsonSchema;
        }

        return [
            'type' => 'function',
            'function' => $function,
        ];
    }
}
