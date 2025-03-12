<?php

declare(strict_types=1);

namespace App\Application\Command;

use Symfony\Component\Validator\Constraints as Assert;

readonly class RemoveWorkerCommand
{
    public function __construct(
        #[Assert\NotBlank] #[Assert\Type('string')] #[Assert\Length(min: 12, max: 40)]
        public ?string $uuid = null,
    ) {
    }
}
