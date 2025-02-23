<?php

declare(strict_types=1);

namespace App\Agent;

class Response
{
    private string $id;
    private string $object;
    private int $created;
    private string $model;
    /**
     * @var array<string, mixed>
     */
    private array $choices;
    /**
     * @var array<string, mixed>
     */
    private array $usage;
    /**
     * @var array<string, mixed>
     */
    private array $rawData = [];

    private ?float $executionTime;

    public function __toString(): string
    {
        return $this->getMessage() ?? '';
    }

    public static function fromArray(array $response, ?float $executionTime = 0): self
    {
        $self = new self();
        $self->rawData = $response;
        $self->id = $response['id'];
        $self->object = $response['object'];
        $self->created = $response['created'];
        $self->model = $response['model'];
        $self->choices = $response['choices'];
        $self->usage = $response['usage'];
        $self->executionTime = $executionTime;

        return $self;
    }

    public function toMessage(RolesEnum $role): Message
    {
        return new Message($role, $this->getMessage());
    }

    public function getMessage(): string
    {
        return $this->choices[0]['message']['content'];
    }

    public function getExecutionTime(): ?float
    {
        return $this->executionTime;
    }

    public function getFinishReason(): string
    {
        return $this->choices[0]['finish_reason'];
    }

    public function getMessageRole(): string
    {
        return $this->choices[0]['message']['role'];
    }

    public function getCreated(): int
    {
        return $this->created;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getObject(): string
    {
        return $this->object;
    }

    public function getUsage(): array
    {
        return $this->usage;
    }

    public function getRawData(): array
    {
        return $this->rawData;
    }

    public function getUsageInfos(): array
    {
        return [
            'id' => $this->id,
            'model' => $this->model,
            'execution_time' => $this->executionTime,
            'prompt_tokens' => $this->usage['prompt_tokens'],
            'completion_tokens' => $this->usage['completion_tokens'],
            'total_tokens' => $this->usage['total_tokens'],
            'finish_reason' => $this->getFinishReason(),
        ];
    }
}
