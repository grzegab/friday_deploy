<?php

declare(strict_types=1);

namespace App\Application\Command;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateCompanyDetailsCommand
{
    private string $uuid;

    public function __construct(
        #[Assert\Type('string')]
        #[Assert\Length(min: 3, max: 250)]
        public readonly ?string $name,
        #[Assert\Type('string')]
        #[Assert\Length(min: 10)]
        public readonly ?string $taxNumber,
        #[Assert\Type('string')]
        #[Assert\Length(min: 3)]
        public readonly ?string $address,
        #[Assert\Type('string')]
        #[Assert\Length(min: 4)]
        public readonly ?string $city,
        #[Assert\Type('string')]
        #[Assert\Length(min: 5, max: 6)]
        public readonly ?string $postcode,
    ) {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }
}
