<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistent\MySQL;

use App\Infrastructure\Persistent\Adapters\MySqlInterface;
use App\Infrastructure\Persistent\Adapters\MysqlQueryInterface;
use App\Infrastructure\Persistent\MySQL\Tables\CompaniesTable;
use App\Infrastructure\Persistent\MySQL\Tables\WorkersTable;

class Core implements MySqlInterface, MysqlQueryInterface
{
    private \PDO $pdo;

    /**
     * @param string[] $options
     */
    public function __construct(string $mysqlDsn, string $mysqlUsername, string $mysqlPassword, array $options = [])
    {
        try {
            $this->pdo = new \PDO($mysqlDsn, $mysqlUsername, $mysqlPassword, $options);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw new \RuntimeException('Failed to connect to the database: '.$e->getMessage(), 0, $e);
        }
    }

    public function createTables(): void
    {
        $queries = [
            CompaniesTable::tableDescription(),
            WorkersTable::tableDescription(),
        ];

        foreach ($queries as $query) {
            $this->pdo->exec($query);
        }
    }

    public function getConnection(): \PDO
    {
        return $this->pdo;
    }

    public function runSimpleQuery(string $query): void
    {
        $this->pdo->query($query);
    }

    public function runFetchQuery(string $query): mixed
    {
        return $this->pdo->query($query)->fetchAll(\PDO::FETCH_ASSOC);
    }
}
