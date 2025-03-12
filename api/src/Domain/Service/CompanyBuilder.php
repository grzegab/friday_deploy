<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Application\Command\CreateCompanyCommand;
use App\Application\Command\UpdateCompanyDetailsCommand;
use App\Domain\Company;
use Symfony\Component\Uid\Uuid;

class CompanyBuilder
{
    public static function createFromCommand(Uuid $uuid, CreateCompanyCommand $dto): Company
    {
        return new Company(
            uuid: $uuid->toString(),
            name: $dto->name,
            taxNumber: $dto->taxNumber,
            address: $dto->address,
            city: $dto->city,
            postcode: $dto->postcode,
        );
    }

    public static function updateFromCommand(Company $company, UpdateCompanyDetailsCommand $command): Company
    {
        return new Company(
            uuid: $company->uuid,
            name: $command->name ?? $company->name,
            taxNumber: $command->taxNumber ?? $company->taxNumber,
            address: $command->address ?? $company->address,
            city: $command->city ?? $company->city,
            postcode: $command->postcode ?? $company->postcode,
        );
    }
}
