<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\Exceptions\CompanyNotExistsException;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\Repository\WorkerRepositoryInterface;
use App\Domain\Service\WorkerBuilder;
use App\Domain\Worker;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
class CreateWorkerCommandHandler
{
    public function __construct(private WorkerRepositoryInterface $workerRepository, private CompanyRepositoryInterface $companyRepository)
    {
    }

    public function __invoke(CreateWorkerCommand $command): Worker
    {
        $companyId = $this->companyRepository->getCompanyId($command->company);

        if (!$companyId) {
            throw new CompanyNotExistsException('Company not found');
        }

        $uuid = Uuid::v7();
        $worker = WorkerBuilder::createFromCommand($uuid, $command);
        $this->workerRepository->persistWorker($worker, $companyId);

        return $worker;
    }
}
