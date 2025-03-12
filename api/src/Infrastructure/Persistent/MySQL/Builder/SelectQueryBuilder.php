<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistent\MySQL\Builder;

class SelectQueryBuilder extends QueryBuilder
{
    /** @var string[] */
    private array $selectValues = [];

    public function addInsertClause(string $parameter, string $value): void
    {
        throw new \LogicException('Insert clause not supported for select query');
    }

    public function addSelectClause(string $parameter, ?string $alias = null): void
    {
        if (empty($alias) && str_contains($parameter, '.')) {
            $alias = substr(strrchr($parameter, '.'), 1) ?: $parameter;
            $this->selectValues[] = "$parameter AS $alias";
        } elseif (!empty($alias)) {
            $this->selectValues[] = "$parameter AS $alias";
        } else {
            $this->selectValues[] = $parameter;
        }
    }

    public function getResult(): string
    {
        $values = implode(', ', array_map(fn ($value) => "$value", array_values($this->selectValues)));

        $statement = sprintf('SELECT %s FROM %s', $values, $this->tableName);

        if ($this->joinValues) {
            foreach ($this->joinValues as $table => $value) {
                $statement .= ' LEFT JOIN '.$table.' ON '.$value;
            }
        }

        if (!empty($this->whereValues)) {
            $statement .= sprintf(' WHERE %s', implode(' AND ', $this->whereValues));
        }

        if ($this->limitOne) {
            $statement .= ' LIMIT 1';
        }

        $statement .= ';';

        return $statement;
    }
}
