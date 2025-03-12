<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Service;

use App\Application\Service\ValidationService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationServiceTest extends TestCase
{
    private ValidatorInterface $validationServiceMock;

    protected function setUp(): void
    {
        $this->validationServiceMock = $this->createMock(ValidatorInterface::class);
    }

    #[Test]
    public function noErrors(): void
    {
        $validateResult = $this->createMock(ConstraintViolationListInterface::class);
        $validateResult->expects($this->once())->method('count')->willReturn(0);
        $this->validationServiceMock->expects($this->once())->method('validate')->willReturn($validateResult);

        $sut = new ValidationService($this->validationServiceMock);
        $result = $sut->validate([]);

        $this->assertEmpty($result);
    }
}
