<?php

declare(strict_types=1);

namespace App\Agent;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;

class AgentBuilder implements AgentBuilderInterface
{
    /**
     * @param ClientInterface&RequestFactoryInterface&StreamFactoryInterface $httpClient
     */
    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        private readonly string $apiKey,
    ) {
    }

    public function build(Message $systemPromptMessage, bool $traceable = true): Agent
    {
        $context = new Context();
        if ($traceable) {
            $context = new TraceableContext($context, $this->logger);
        }

        return (new Agent(
            $this->httpClient,
            $context,
            $this->logger,
            $this->apiKey
        ))->create($systemPromptMessage);
    }
}
