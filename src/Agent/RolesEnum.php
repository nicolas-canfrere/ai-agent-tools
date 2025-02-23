<?php

declare(strict_types=1);

namespace App\Agent;

enum RolesEnum: string
{
    case SYSTEM = 'system';
    case ASSISTANT = 'assistant';
    case USER = 'user';
}
