<?php

declare(strict_types=1);

namespace App\Agent;

interface AgentBuilderInterface
{
    public function build(Message $systemPromptMessage, bool $traceable = true): Agent;
}
