<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Domain\Company;
use App\Domain\Repository\CompanyRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CompanyDetailsQueryHandler
{
    public function __construct(private CompanyRepositoryInterface $repository)
    {
    }

    public function __invoke(CompanyDetailsQuery $query): Company
    {
        return $this->repository->getCompanyDetails($query->uuid);
    }
}
