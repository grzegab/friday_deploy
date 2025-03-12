<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Domain\Repository\WorkerRepositoryInterface;
use App\Domain\Worker;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class WorkerListQueryHandler
{
    public function __construct(private WorkerRepositoryInterface $repository)
    {
    }

    /**
     * @return Worker[]
     */
    public function __invoke(WorkerListQuery $query): array
    {
        return $this->repository->getWorkerList($query->uuid);
    }
}
