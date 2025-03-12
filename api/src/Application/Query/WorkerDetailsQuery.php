<?php

declare(strict_types=1);

namespace App\Application\Query;

use Symfony\Component\Validator\Constraints as Assert;

readonly class WorkerDetailsQuery
{
    public function __construct(
        #[Assert\NotBlank] #[Assert\Type('string')] #[Assert\Length(min: 12, max: 40)]
        public ?string $uuid = null,
    ) {
    }
}
