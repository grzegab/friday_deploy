<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistent\MySQL\Builder;

abstract class QueryBuilder implements QueryBuilderInterface
{
    protected string $tableName;
    protected bool $limitOne = false;
    /** @var string[] */
    protected array $whereValues = [];
    /** @var string[] */
    protected array $joinValues = [];

    public function setTable(string $tableName): void
    {
        $this->tableName = $tableName;
    }

    public function selectFirst(): void
    {
        $this->limitOne = true;
    }

    public function addWhereClause(string $parameter, string $value): void
    {
        $this->whereValues[] = "$parameter = '$value'";
    }

    public function joinTable(string $tableName, string $joinParameter, string $joinValue): void
    {
        $this->joinValues[$tableName] = "$tableName.$joinParameter = $joinValue";
    }

    abstract public function getResult(): string;
}
