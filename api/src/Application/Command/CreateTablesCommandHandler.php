<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Infrastructure\Persistent\Adapters\MySqlInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateTablesCommandHandler
{
    public function __construct(private MySqlInterface $mysql)
    {
    }

    public function __invoke(CreateTablesCommand $command): void
    {
        $this->mysql->createTables();
    }
}
