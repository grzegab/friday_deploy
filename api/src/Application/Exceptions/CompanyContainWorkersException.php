<?php

declare(strict_types=1);

namespace App\Application\Exceptions;

class CompanyContainWorkersException extends \RuntimeException
{
    public function __construct(string $message = 'Company has workers. Please remove them first.', int $code = 422, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
