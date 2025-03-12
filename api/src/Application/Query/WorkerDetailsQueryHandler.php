<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Domain\Repository\WorkerRepositoryInterface;
use App\Domain\Worker;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class WorkerDetailsQueryHandler
{
    public function __construct(private WorkerRepositoryInterface $repository)
    {
    }

    public function __invoke(WorkerDetailsQuery $query): Worker
    {
        return $this->repository->getWorkerDetails($query->uuid);
    }
}
