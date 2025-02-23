<?php

declare(strict_types=1);

namespace App\Command;

use App\Agent\Agent;
use App\Agent\Message;
use App\Agent\RolesEnum;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'agent:run',
    description: 'Add a short description for your command',
)]
class AgentRunCommand extends Command
{
    public function __construct(
        private readonly Agent $agent,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->agent
            ->create(new Message(RolesEnum::SYSTEM, 'You are a useful assistant.'))
        ;
        $response = $this->agent->run(new Message(RolesEnum::USER, 'Explains what is a black hole.'));

        $io->success((string) $response);

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
