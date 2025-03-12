<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistent;

use App\Application\Exceptions\WorkerNotExistsException;
use App\Domain\Repository\WorkerRepositoryInterface;
use App\Domain\Worker;
use App\Infrastructure\Persistent\Adapters\MysqlQueryInterface;
use App\Infrastructure\Persistent\Mappers\WorkerMapper;
use App\Infrastructure\Persistent\MySQL\Builder\InsertQueryBuilder;
use App\Infrastructure\Persistent\MySQL\Builder\RemoveQueryBuilder;
use App\Infrastructure\Persistent\MySQL\Builder\SelectQueryBuilder;
use App\Infrastructure\Persistent\MySQL\Builder\UpdateQueryBuilder;
use App\Infrastructure\Persistent\MySQL\Tables\CompaniesTable;
use App\Infrastructure\Persistent\MySQL\Tables\WorkersTable;

readonly class WorkerService implements WorkerRepositoryInterface
{
    public function __construct(private MysqlQueryInterface $mysql)
    {
    }

    public function persistWorker(Worker $worker, int $companyId): void
    {
        $builder = new InsertQueryBuilder();
        $builder->setTable(WorkersTable::TABLE);
        $builder->addInsertClause(WorkersTable::UUID, $worker->uuid);
        $builder->addInsertClause(WorkersTable::FIRST_NAME, $worker->name);
        $builder->addInsertClause(WorkersTable::LAST_NAME, $worker->surname);
        $builder->addInsertClause(WorkersTable::EMAIL, $worker->email);
        $builder->addInsertClause(WorkersTable::COMPANY_ID, (string) $companyId);

        if (null !== $worker->phone) {
            $builder->addInsertClause(WorkersTable::PHONE, $worker->phone);
        }

        $query = $builder->getResult();

        $this->mysql->runSimpleQuery($query);
    }

    /**
     * @return Worker[]
     */
    public function getWorkerList(string $companyUuid): array
    {
        $builder = new SelectQueryBuilder();
        $builder->setTable(WorkersTable::TABLE);
        $builder->joinTable(CompaniesTable::TABLE, CompaniesTable::ID, WorkersTable::COMPANY_ID);
        $builder->addSelectClause(sprintf('%s.%s', WorkersTable::TABLE, WorkersTable::UUID));
        $builder->addSelectClause(sprintf('%s.%s', WorkersTable::TABLE, WorkersTable::FIRST_NAME));
        $builder->addSelectClause(sprintf('%s.%s', WorkersTable::TABLE, WorkersTable::LAST_NAME));
        $builder->addSelectClause(sprintf('%s.%s', WorkersTable::TABLE, WorkersTable::PHONE));
        $builder->addSelectClause(sprintf('%s.%s', WorkersTable::TABLE, WorkersTable::EMAIL));
        $builder->addSelectClause(sprintf('%s.%s', CompaniesTable::TABLE, CompaniesTable::UUID), WorkersTable::COMPANY_ID);
        $builder->addWhereClause(sprintf('%s.%s', CompaniesTable::TABLE, CompaniesTable::UUID), $companyUuid);
        $query = $builder->getResult();
        $workersRaw = $this->mysql->runFetchQuery($query);

        $result = [];
        foreach ($workersRaw as $workerRaw) {
            $result[] = WorkerMapper::createFrom($workerRaw);
        }

        return $result;
    }

    public function getWorkerDetails(string $workerUuid): Worker
    {
        $builder = new SelectQueryBuilder();
        $builder->setTable(WorkersTable::TABLE);
        $builder->joinTable(CompaniesTable::TABLE, CompaniesTable::ID, WorkersTable::COMPANY_ID);
        $builder->addSelectClause(sprintf('%s.%s', WorkersTable::TABLE, WorkersTable::UUID));
        $builder->addSelectClause(sprintf('%s.%s', WorkersTable::TABLE, WorkersTable::FIRST_NAME));
        $builder->addSelectClause(sprintf('%s.%s', WorkersTable::TABLE, WorkersTable::LAST_NAME));
        $builder->addSelectClause(sprintf('%s.%s', WorkersTable::TABLE, WorkersTable::PHONE));
        $builder->addSelectClause(sprintf('%s.%s', WorkersTable::TABLE, WorkersTable::EMAIL));
        $builder->addSelectClause(sprintf('%s.%s', CompaniesTable::TABLE, CompaniesTable::UUID), WorkersTable::COMPANY_ID);
        $builder->addWhereClause(sprintf('%s.%s', WorkersTable::TABLE, WorkersTable::UUID), $workerUuid);
        $query = $builder->getResult();
        $workerRaw = $this->mysql->runFetchQuery($query);

        if (empty($workerRaw)) {
            throw new WorkerNotExistsException();
        }

        return WorkerMapper::createFrom($workerRaw[0]);
    }

    public function updateWorkerDetails(Worker $worker): void
    {
        $builder = new UpdateQueryBuilder();
        $builder->setTable(WorkersTable::TABLE);
        $builder->addInsertClause(WorkersTable::FIRST_NAME, $worker->name);
        $builder->addInsertClause(WorkersTable::LAST_NAME, $worker->surname);
        $builder->addInsertClause(WorkersTable::EMAIL, $worker->email);
        $builder->addWhereClause(WorkersTable::UUID, $worker->uuid);

        if (null !== $worker->phone) {
            $builder->addInsertClause(WorkersTable::PHONE, $worker->phone);
        }

        $query = $builder->getResult();

        $this->mysql->runSimpleQuery($query);
    }

    public function removeWorker(Worker $worker): void
    {
        $builder = new RemoveQueryBuilder();
        $builder->setTable(WorkersTable::TABLE);
        $builder->addWhereClause(WorkersTable::UUID, $worker->uuid);
        $queryStatement = $builder->getResult();

        $this->mysql->runSimpleQuery($queryStatement);
    }
}
