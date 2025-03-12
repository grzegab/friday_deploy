<?php

declare(strict_types=1);

namespace App\Application\Command;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateWorkerCommand
{
    private string $uuid;

    public function __construct(
        #[Assert\Type('string')]
        #[Assert\Length(min: 3, max: 250)]
        public ?string $firstName,
        #[Assert\Type('string')]
        #[Assert\Length(min: 3)]
        public ?string $lastName,
        #[Assert\Type('string')]
        #[Assert\Length(min: 4)]
        public ?string $email,
        #[Assert\Type('string')]
        #[Assert\Length(min: 4)]
        public ?string $phone,
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
