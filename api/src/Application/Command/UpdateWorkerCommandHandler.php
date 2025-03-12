<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\Repository\WorkerRepositoryInterface;
use App\Domain\Service\WorkerBuilder;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateWorkerCommandHandler
{
    public function __construct(private WorkerRepositoryInterface $repository)
    {
    }

    public function __invoke(UpdateWorkerCommand $command): string
    {
        $worker = $this->repository->getWorkerDetails($command->getUuid());
        $updatedWorker = WorkerBuilder::updateFromCommand($worker, $command);

        $this->repository->updateWorkerDetails($updatedWorker);

        return $worker->uuid;
    }
}
