<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistent\MySQL\Builder;

class RemoveQueryBuilder extends QueryBuilder
{
    public function getResult(): string
    {
        if (empty($this->whereValues)) {
            throw new \RuntimeException('Where clause is required for remove query');
        }

        $statement = sprintf('DELETE FROM %s WHERE %s', $this->tableName, implode(' AND ', $this->whereValues));

        $statement .= ';';

        return $statement;
    }

    public function addInsertClause(string $parameter, string $value): void
    {
        throw new \LogicException('Insert clause not supported for remove query');
    }

    public function addSelectClause(string $parameter, ?string $alias = null): void
    {
        throw new \LogicException('Select clause not supported for remove query');
    }
}
