<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\Repository\WorkerRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RemoveWorkerCommandHandler
{
    public function __construct(private WorkerRepositoryInterface $workerRepository)
    {
    }

    public function __invoke(RemoveWorkerCommand $command): string
    {
        $worker = $this->workerRepository->getWorkerDetails($command->uuid);
        $this->workerRepository->removeWorker($worker);

        return $worker->uuid;
    }
}
