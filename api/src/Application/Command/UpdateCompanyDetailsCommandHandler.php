<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\Company;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\Service\CompanyBuilder;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateCompanyDetailsCommandHandler
{
    public function __construct(private CompanyRepositoryInterface $companyRepository)
    {
    }

    public function __invoke(UpdateCompanyDetailsCommand $command): Company
    {
        $company = $this->companyRepository->getCompanyDetails($command->getUuid());
        $updatedCompany = CompanyBuilder::updateFromCommand($company, $command);

        $this->companyRepository->updateCompanyDetails($updatedCompany);

        return $updatedCompany;
    }
}
