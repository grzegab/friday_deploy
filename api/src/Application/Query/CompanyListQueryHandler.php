<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Domain\Company;
use App\Domain\Repository\CompanyRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CompanyListQueryHandler
{
    public function __construct(private CompanyRepositoryInterface $repository)
    {
    }

    /**
     * @return Company[]
     */
    public function __invoke(CompanyListQuery $query): array
    {
        return $this->repository->getCompanyList();
    }
}
