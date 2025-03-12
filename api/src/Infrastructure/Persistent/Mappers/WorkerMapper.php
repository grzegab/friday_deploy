<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistent\Mappers;

use App\Domain\Worker;
use App\Infrastructure\Persistent\MySQL\Tables\WorkersTable;

class WorkerMapper
{
    /**
     * @param string[] $databaseData
     */
    public static function createFrom(array $databaseData): Worker
    {
        return new Worker(
            uuid: $databaseData[WorkersTable::UUID],
            companyUuid: $databaseData[WorkersTable::COMPANY_ID] ?? null,
            name: $databaseData[WorkersTable::FIRST_NAME],
            surname: $databaseData[WorkersTable::LAST_NAME],
            email: $databaseData[WorkersTable::EMAIL],
            phone: $databaseData[WorkersTable::PHONE],
        );
    }
}
