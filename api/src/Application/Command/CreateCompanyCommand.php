<?php

declare(strict_types=1);

namespace App\Application\Command;

use Symfony\Component\Validator\Constraints as Assert;

readonly class CreateCompanyCommand
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 3, max: 250)]
        public string $name,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 10)]
        public string $taxNumber,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 3)]
        public string $address,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 4)]
        public string $city,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 5, max: 6)]
        public string $postcode,
    ) {
    }
}
