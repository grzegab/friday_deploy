<?php

declare(strict_types=1);

namespace App\Domain;

use Symfony\Component\Serializer\Annotation\Groups;

class Company
{
    #[Groups(['full'])]
    public string $id;

    #[Groups(['list', 'created', 'removed', 'details', 'updated'])]
    public readonly string $uuid;

    #[Groups(['list', 'details', 'updated'])]
    public readonly string $name;

    #[Groups(['list', 'details', 'updated'])]
    public readonly string $taxNumber;

    #[Groups(['details', 'updated'])]
    public readonly string $address;

    #[Groups(['details', 'updated'])]
    public readonly string $city;

    #[Groups(['details', 'updated'])]
    public readonly string $postcode;

    /** @var Worker[] */
    #[Groups(['details'])]
    public array $workers;

    public function __construct(
        string $uuid,
        string $name,
        string $taxNumber,
        string $address,
        string $city,
        string $postcode,
    ) {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->taxNumber = $taxNumber;
        $this->address = $address;
        $this->city = $city;
        $this->postcode = $postcode;
    }

    /**
     * @return Worker[]
     */
    public function getWorkers(): array
    {
        return $this->workers;
    }
}
