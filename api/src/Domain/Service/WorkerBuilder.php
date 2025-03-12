<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Application\Command\CreateWorkerCommand;
use App\Application\Command\UpdateWorkerCommand;
use App\Domain\Worker;
use Symfony\Component\Uid\Uuid;

class WorkerBuilder
{
    public static function createFromCommand(Uuid $uuid, CreateWorkerCommand $command): Worker
    {
        return new Worker(
            uuid: $uuid->toString(),
            companyUuid: $command->company,
            name: $command->firstName,
            surname: $command->lastName,
            email: $command->email,
            phone: $command->phone
        );
    }

    public static function updateFromCommand(Worker $worker, UpdateWorkerCommand $command): Worker
    {
        return new Worker(
            uuid: $worker->uuid,
            companyUuid: $worker->companyUuid,
            name: $command->firstName ?? $worker->name,
            surname: $command->lastName ?? $worker->surname,
            email: $command->email ?? $worker->email,
            phone: $command->phone ?? $worker->phone,
        );
    }
}
