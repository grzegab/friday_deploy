<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\Company;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\Service\CompanyBuilder;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
readonly class CreateCompanyCommandHandler
{
    public function __construct(private CompanyRepositoryInterface $companyRepository)
    {
    }

    public function __invoke(CreateCompanyCommand $command): Company
    {
        $uuid = Uuid::v7();
        $company = CompanyBuilder::createFromCommand($uuid, $command);
        $this->companyRepository->persistCompany($company);

        return $company;
    }
}
