<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Persistent;

use App\Domain\Company;
use App\Domain\Worker;
use App\Infrastructure\Persistent\CompanyService;
use App\Infrastructure\Persistent\MySQL\Core;
use App\Infrastructure\Persistent\WorkerService;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Uid\Uuid;

class WorkerServiceTest extends WebTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    protected function tearDown(): void
    {
        $container = static::getContainer();
        $mysql = $container->get(Core::class);
        $mysql->runSimpleQuery('DELETE FROM workers');
        $mysql->runSimpleQuery('DELETE FROM companies');
    }

    #[Test]
    public function persistWorker(): void
    {
        $container = static::getContainer();
        $companyService = $container->get(CompanyService::class);
        $workerService = $container->get(WorkerService::class);
        $companyUuid = Uuid::v7()->toString();
        $company = new Company($companyUuid, 'Nazwa', '1231231230', 'Adres', 'Miasto', '00-001');
        $companyService->persistCompany($company);

        $mysql = $container->get(Core::class);
        $r = $mysql->runFetchQuery('SELECT * FROM companies');
        $companyId = $r[0]['id'];

        $this->assertNotEmpty($r);
        $this->assertCount(1, $r);

        $worker1 = new Worker(Uuid::v7()->toString(), $companyUuid, 'Jan', 'Kowalski', 'jan@kowal.ski');
        $workerService->persistWorker($worker1, $companyId);
        $worker2 = new Worker(Uuid::v7()->toString(), $companyUuid, 'Maj', 'Iski', 'isk@iki.ski');
        $workerService->persistWorker($worker2, $companyId);

        $r2 = $mysql->runFetchQuery('SELECT * FROM workers');

        $this->assertNotEmpty($r2);
        $this->assertCount(2, $r2);

    }

    #[Test]
    public function updateWorker(): void
    {
        $container = static::getContainer();
        $companyService = $container->get(CompanyService::class);
        $workerService = $container->get(WorkerService::class);
        $companyUuid = Uuid::v7()->toString();
        $company = new Company($companyUuid, 'Nazwa', '1231231230', 'Adres', 'Miasto', '00-001');
        $companyService->persistCompany($company);

        $mysql = $container->get(Core::class);
        $r = $mysql->runFetchQuery('SELECT * FROM companies');
        $companyId = $r[0]['id'];

        $this->assertNotEmpty($r);
        $this->assertCount(1, $r);

        $worker1 = new Worker(Uuid::v7()->toString(), $companyUuid, 'Jan', 'Kowalski', 'jan@kowal.ski');
        $workerService->persistWorker($worker1, $companyId);
        $worker2 = new Worker(Uuid::v7()->toString(), $companyUuid, 'Maj', 'Iski', 'isk@iki.ski');
        $workerService->persistWorker($worker2, $companyId);

        $worker1a = new Worker($worker1->uuid, $worker1->companyUuid, 'Kran', 'Wan', 'kran@wan.ski');
        $workerService->updateWorkerDetails($worker1a);

        $r2 = $mysql->runFetchQuery('SELECT * FROM workers');

        $this->assertNotEmpty($r2);
        $this->assertCount(2, $r2);

        $this->assertSame('Kran', $r2[0]['first_name']);
        $this->assertSame('Wan', $r2[0]['last_name']);
        $this->assertSame('kran@wan.ski', $r2[0]['email']);
        $this->assertSame('Maj', $r2[1]['first_name']);
        $this->assertSame('Iski', $r2[1]['last_name']);
        $this->assertSame('isk@iki.ski', $r2[1]['email']);


    }
}
