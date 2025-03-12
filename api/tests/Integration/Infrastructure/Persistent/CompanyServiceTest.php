<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Persistent;

use App\Domain\Company;
use App\Infrastructure\Persistent\CompanyService;
use App\Infrastructure\Persistent\MySQL\Core;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Uid\Uuid;

class CompanyServiceTest extends WebTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    protected function tearDown(): void
    {
        $container = static::getContainer();
        $mysql = $container->get(Core::class);
        $mysql->runSimpleQuery('DELETE FROM companies');
    }

    #[Test]
    public function persistingCompany(): void
    {
        $container = static::getContainer();
        $companyService = $container->get(CompanyService::class);
        $company = new Company(Uuid::v7()->toString(), 'Nazwa', '1231231230', 'Adres', 'Miasto', '00-001');
        $companyService->persistCompany($company);

        $mysql = $container->get(Core::class);
        $r = $mysql->runFetchQuery('SELECT * FROM companies');

        $this->assertNotEmpty($r);
        $this->assertCount(1, $r);
    }

    #[Test]
    public function updateCompany(): void
    {
        $container = static::getContainer();
        $companyService = $container->get(CompanyService::class);
        $company = new Company(Uuid::v7()->toString(), 'Nazwa', '1231231230', 'Adres', 'Miasto', '00-001');
        $companyService->persistCompany($company);

        $mysql = $container->get(Core::class);
        $r = $mysql->runFetchQuery('SELECT * FROM companies');

        $this->assertNotEmpty($r);
        $this->assertCount(1, $r);

        $company = new Company($company->uuid, 'Nazwa 2', $company->taxNumber, 'Adres 2', 'Miasto 2', '00-001');

        $companyService->updateCompanyDetails($company);

        $r2 = $mysql->runFetchQuery('SELECT * FROM companies');
        $this->assertCount(1, $r2);
        $this->assertSame('Nazwa 2', $r2[0]['company_name']);
        $this->assertSame('Adres 2', $r2[0]['address']);
    }
}
