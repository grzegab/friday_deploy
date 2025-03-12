<?php

namespace App\Infrastructure\Persistent\MySQL\Builder;

interface QueryBuilderInterface
{
    public function setTable(string $tableName): void;

    public function addInsertClause(string $parameter, string $value): void;

    public function addSelectClause(string $parameter, ?string $alias = null): void;

    public function selectFirst(): void;

    public function getResult(): string;
}
