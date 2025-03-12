<?php

namespace App\Infrastructure\Persistent\Adapters;

interface MysqlQueryInterface
{
    public function runSimpleQuery(string $query): void;

    public function runFetchQuery(string $query): mixed;
}
