<?php

declare(strict_types=1);

namespace App\Application\Command;

use Symfony\Component\Validator\Constraints as Assert;

class CreateWorkerCommand
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 10, max: 40)]
        public string $company,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 3, max: 250)]
        public string $firstName,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 3)]
        public string $lastName,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 4)]
        public string $email,
        #[Assert\Type('string')]
        #[Assert\Length(min: 4)]
        public ?string $phone = null,
    ) {
    }
}
