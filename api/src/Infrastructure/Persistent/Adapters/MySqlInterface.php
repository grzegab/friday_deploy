<?php

namespace App\Infrastructure\Persistent\Adapters;

interface MySqlInterface
{
    public function getConnection(): \PDO;

    public function createTables(): void;
}
