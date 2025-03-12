<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\Exceptions\CompanyContainWorkersException;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\Repository\WorkerRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RemoveCompanyCommandHandler
{
    public function __construct(private CompanyRepositoryInterface $companyRepository, private WorkerRepositoryInterface $workerRepository)
    {
    }

    public function __invoke(RemoveCompanyCommand $command): string
    {
        $workers = $this->workerRepository->getWorkerList($command->uuid);
        if (!empty($workers)) {
            throw new CompanyContainWorkersException();
        }

        $company = $this->companyRepository->getCompanyDetails($command->uuid);
        $this->companyRepository->removeCompany($company);

        return $company->uuid;
    }
}
