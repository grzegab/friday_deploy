<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistent\MySQL\Builder;

class UpdateQueryBuilder extends QueryBuilder
{
    /** @var string[] */
    private array $updateValues = [];

    public function addInsertClause(string $parameter, string $value): void
    {
        $this->updateValues[$parameter] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public function addSelectClause(string $parameter, ?string $alias = null): void
    {
        throw new \LogicException('Select clause not supported for update query');
    }

    public function selectFirst(): void
    {
        throw new \LogicException('Select first not supported for update query');
    }

    public function getResult(): string
    {
        $setValues = array_map(
            static fn ($key, $value) => sprintf("%s = '%s'", $key, $value),
            array_keys($this->updateValues),
            $this->updateValues
        );

        $statement = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $this->tableName,
            implode(', ', $setValues),
            implode(' AND ', $this->whereValues)
        );

        $statement .= ';';

        return $statement;
    }
}
