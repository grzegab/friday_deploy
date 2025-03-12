<?php

declare(strict_types=1);

namespace App\Domain;

use Symfony\Component\Serializer\Annotation\Groups;

readonly class Worker
{
    #[Groups(['list', 'created', 'removed', 'updated', 'details'])]
    public string $uuid;

    #[Groups(['list', 'removed', 'updated', 'details'])]
    public string $companyUuid;

    #[Groups(['list', 'created', 'updated', 'details'])]
    public string $name;

    #[Groups(['list', 'created', 'updated', 'details'])]
    public string $surname;

    #[Groups(['details', 'updated'])]
    public string $email;

    #[Groups(['details', 'updated'])]
    public ?string $phone;

    public function __construct(string $uuid, string $companyUuid, string $name, string $surname, string $email, ?string $phone = null)
    {
        $this->uuid = $uuid;
        $this->companyUuid = $companyUuid;
        $this->name = $name;
        $this->surname = $surname;
        $this->email = $email;
        $this->phone = $phone;
    }
}
