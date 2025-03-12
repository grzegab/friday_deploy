<?php

namespace App\Domain\Repository;

use App\Domain\Company;

interface CompanyRepositoryInterface
{
    public function getCompanyId(string $uuid): ?int;

    public function persistCompany(Company $company): void;

    public function updateCompanyDetails(Company $company): void;

    public function removeCompany(Company $company): void;

    public function getCompanyDetails(string $uuid): Company;

    /**
     * @return Company[]
     */
    public function getCompanyList(): array;
}
