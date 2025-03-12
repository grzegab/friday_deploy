<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Persistent\Mappers;

use App\Infrastructure\Persistent\Mappers\CompanyMapper;
use App\Infrastructure\Persistent\MySQL\Tables\CompaniesTable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CompanyMapperTest extends TestCase
{
    #[Test]
    public function checkForLogicException(): void
    {
        $this->expectException(\LogicException::class);

        $sut = new CompanyMapper();
        $sut::createCompanyFrom([CompaniesTable::UUID => null]);
    }
}
