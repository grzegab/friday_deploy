<?php

namespace App\Domain\Repository;

use App\Domain\Worker;

interface WorkerRepositoryInterface
{
    public function persistWorker(Worker $worker, int $companyId): void;

    /**
     * @return Worker[]
     */
    public function getWorkerList(string $companyUuid): array;

    public function getWorkerDetails(string $workerUuid): Worker;

    public function updateWorkerDetails(Worker $worker): void;

    public function removeWorker(Worker $worker): void;
}
