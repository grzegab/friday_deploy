<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistent;

use App\Application\Exceptions\CompanyNotExistsException;
use App\Domain\Company;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Infrastructure\Persistent\Adapters\MysqlQueryInterface;
use App\Infrastructure\Persistent\Mappers\CompanyMapper;
use App\Infrastructure\Persistent\MySQL\Builder\InsertQueryBuilder;
use App\Infrastructure\Persistent\MySQL\Builder\RemoveQueryBuilder;
use App\Infrastructure\Persistent\MySQL\Builder\SelectQueryBuilder;
use App\Infrastructure\Persistent\MySQL\Builder\UpdateQueryBuilder;
use App\Infrastructure\Persistent\MySQL\Tables\CompaniesTable;

readonly class CompanyService implements CompanyRepositoryInterface
{
    public function __construct(private MysqlQueryInterface $mysql)
    {
    }

    public function getCompanyId(string $uuid): ?int
    {
        $builder = new SelectQueryBuilder();
        $builder->setTable(CompaniesTable::TABLE);
        $builder->addSelectClause(CompaniesTable::ID);
        $query = $builder->getResult();
        $companiesRaw = $this->mysql->runFetchQuery($query);

        if (empty($companiesRaw[0]['id'])) {
            return null;
        }

        return $companiesRaw[0]['id'];
    }

    public function persistCompany(Company $company): void
    {
        $builder = new InsertQueryBuilder();
        $builder->setTable(CompaniesTable::TABLE);
        $builder->addInsertClause(CompaniesTable::UUID, $company->uuid);
        $builder->addInsertClause(CompaniesTable::COMPANY_NAME, $company->name);
        $builder->addInsertClause(CompaniesTable::TAX_NUMBER, $company->taxNumber);
        $builder->addInsertClause(CompaniesTable::ADDRESS, $company->address);
        $builder->addInsertClause(CompaniesTable::CITY, $company->city);
        $builder->addInsertClause(CompaniesTable::POSTCODE, $company->postcode);
        $query = $builder->getResult();

        $this->mysql->runSimpleQuery($query);
    }

    public function updateCompanyDetails(Company $company): void
    {
        $builder = new UpdateQueryBuilder();
        $builder->setTable(CompaniesTable::TABLE);
        $builder->addInsertClause(CompaniesTable::COMPANY_NAME, $company->name);
        $builder->addInsertClause(CompaniesTable::TAX_NUMBER, $company->taxNumber);
        $builder->addInsertClause(CompaniesTable::ADDRESS, $company->address);
        $builder->addInsertClause(CompaniesTable::CITY, $company->city);
        $builder->addInsertClause(CompaniesTable::POSTCODE, $company->postcode);
        $builder->addWhereClause(CompaniesTable::UUID, $company->uuid);
        $query = $builder->getResult();

        $this->mysql->runSimpleQuery($query);
    }

    /**
     * @return Company[]
     */
    public function getCompanyList(): array
    {
        $builder = new SelectQueryBuilder();
        $builder->setTable(CompaniesTable::TABLE);
        $builder->addSelectClause(CompaniesTable::UUID);
        $builder->addSelectClause(CompaniesTable::COMPANY_NAME);
        $builder->addSelectClause(CompaniesTable::TAX_NUMBER);
        $builder->addSelectClause(CompaniesTable::ADDRESS);
        $builder->addSelectClause(CompaniesTable::CITY);
        $builder->addSelectClause(CompaniesTable::POSTCODE);
        $query = $builder->getResult();

        $companiesRaw = $this->mysql->runFetchQuery($query);
        $result = [];
        foreach ($companiesRaw as $companyRaw) {
            $result[] = CompanyMapper::createCompanyFrom($companyRaw);
        }

        return $result;
    }

    public function getCompanyDetails(string $uuid): Company
    {
        $builder = new SelectQueryBuilder();
        $builder->setTable(CompaniesTable::TABLE);
        $builder->addSelectClause(CompaniesTable::UUID);
        $builder->addSelectClause(CompaniesTable::COMPANY_NAME);
        $builder->addSelectClause(CompaniesTable::TAX_NUMBER);
        $builder->addSelectClause(CompaniesTable::ADDRESS);
        $builder->addSelectClause(CompaniesTable::CITY);
        $builder->addSelectClause(CompaniesTable::POSTCODE);
        $builder->addWhereClause(CompaniesTable::UUID, $uuid);

        $query = $builder->getResult();
        $rawDetails = $this->mysql->runFetchQuery($query);
        if (empty($rawDetails)) {
            throw new CompanyNotExistsException();
        }

        return CompanyMapper::createCompanyFrom($rawDetails[0]);
    }

    public function removeCompany(Company $company): void
    {
        $builder = new RemoveQueryBuilder();
        $builder->setTable(CompaniesTable::TABLE);
        $builder->addWhereClause(CompaniesTable::UUID, $company->uuid);
        $queryStatement = $builder->getResult();

        $this->mysql->runSimpleQuery($queryStatement);
    }
}
