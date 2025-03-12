<?php

declare(strict_types=1);

namespace App\Application\Exceptions;

class CompanyNotExistsException extends \RuntimeException
{
    public function __construct(string $message = 'Company not found', int $code = 404, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
