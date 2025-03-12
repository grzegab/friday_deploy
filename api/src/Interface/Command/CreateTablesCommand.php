<?php

declare(strict_types=1);

namespace App\Interface\Command;

use App\Application\Command\CreateTablesCommand as ApplicationCreateTablesCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(name: 'app:create-tables', description: 'Create MySQL tables')]
class CreateTablesCommand extends Command
{
    public function __construct(private readonly MessageBusInterface $messageBus)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $command = new ApplicationCreateTablesCommand();
        $this->messageBus->dispatch($command);

        return Command::SUCCESS;
    }
}
