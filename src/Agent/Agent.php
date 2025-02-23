<?php

declare(strict_types=1);

namespace App\Agent;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;

class Agent
{
    /**
     * @var array<Tool>
     */
    private array $tools = [];

    /**
     * @param ClientInterface&RequestFactoryInterface&StreamFactoryInterface $httpClient
     */
    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly ContextInterface $context,
        private readonly LoggerInterface $logger,
        private readonly string $apiKey,
    ) {
    }

    public function addTool(Tool $tool): void
    {
        $this->tools[] = $tool;
    }

    public function create(Message $systemPromptMessage): self
    {
        $this->context->append($systemPromptMessage);

        return $this;
    }

    public function run(?Message $message): string
    {
        if ($this->context->isEmpty()) {
            throw new \RuntimeException('The context is empty. You need to create before with a system prompt.');
        }
        if ($message) {
            $this->context->append($message);
        }
        $response = $this->execute();
        $this->context->append($response->toMessage(RolesEnum::ASSISTANT));

        return $response->getMessage();
    }

    public function execute(): Response
    {
        $request = $this->httpClient->createRequest('POST', 'https://api.openai.com/v1/chat/completions');
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withHeader('Authorization', sprintf('Bearer %s', $this->apiKey));
        $request = $request->withBody($this->httpClient->createStream($this->createBody()));

        try {
            $start = -microtime(true);
            $response = $this->httpClient->sendRequest($request);
            $content = \json_decode($response->getBody()->getContents(), true);
            $executionTime = $start + microtime(true);
            $response = Response::fromArray($content, $executionTime);
            $this->logger->debug(
                'Request executed',
                $response->getUsageInfos()
            );

            return $response;
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to execute the request with message: ' . $e->getMessage(), 0, $e);
        }
    }

    public function createBody(): string
    {
        $body = [
            'model' => 'gpt-4o-mini',
            'messages' => $this->context->getMessages(),
            'temperature' => 0.1,
        ];
        if (!empty($this->tools)) {
            $body['tools'] = $this->tools;
            $body['tool_choice'] = 'auto';
        }

        return \json_encode($body);
    }
}
