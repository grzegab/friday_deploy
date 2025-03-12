<?php

declare(strict_types=1);

namespace App\Application\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationService
{
    public function __construct(private ValidatorInterface $validatorService)
    {
    }

    /**
     * @return array<string, array<string, string|\Stringable>>|null
     */
    public function validate(mixed $input): ?array
    {
        $validationResult = $this->validatorService->validate($input);
        if ($validationResult->count() > 0) {
            $errors = [];
            foreach ($validationResult as $validationError) {
                $errors[$validationError->getPropertyPath()] = $validationError->getMessage();
            }

            return ['errors' => $errors];
        }

        return null;
    }
}
