<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistent\Mappers;

use App\Domain\Company;
use App\Infrastructure\Persistent\MySQL\Tables\CompaniesTable;

class CompanyMapper
{
    /**
     * @param string[] $companyData
     */
    public static function createCompanyFrom(array $companyData): Company
    {
        foreach ($companyData as $value) {
            self::checkForEmpty($value);
        }

        return new Company(
            uuid: $companyData[CompaniesTable::UUID],
            name: $companyData[CompaniesTable::COMPANY_NAME],
            taxNumber: $companyData[CompaniesTable::TAX_NUMBER],
            address: $companyData[CompaniesTable::ADDRESS],
            city: $companyData[CompaniesTable::CITY],
            postcode: $companyData[CompaniesTable::POSTCODE],
        );
    }

    private static function checkForEmpty(mixed $value): void
    {
        if ($value === null) {
            throw new \LogicException('Company value cannot be null');
        }
    }
}
