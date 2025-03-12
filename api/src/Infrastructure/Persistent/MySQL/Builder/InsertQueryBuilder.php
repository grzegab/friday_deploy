<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistent\MySQL\Builder;

class InsertQueryBuilder extends QueryBuilder
{
    /** @var string[] */
    private array $insertValues = [];

    public function addInsertClause(string $parameter, string $value): void
    {
        $this->insertValues[$parameter] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public function addSelectClause(string $parameter, ?string $alias = null): void
    {
        throw new \LogicException('Select clause not supported for insert query');
    }

    public function getResult(): string
    {
        $keys = implode(', ', array_map(fn ($value) => "`$value`", array_keys($this->insertValues)));
        $values = implode(', ', array_map(fn ($value) => "'$value'", array_values($this->insertValues)));

        return sprintf('INSERT INTO %s (%s) VALUES (%s)', $this->tableName, $keys, $values);
    }

    public function selectFirst(): void
    {
        throw new \LogicException('Select first not supported for insert query');
    }
}
