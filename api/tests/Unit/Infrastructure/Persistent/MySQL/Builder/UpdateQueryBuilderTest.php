<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Persistent\MySQL\Builder;

use App\Infrastructure\Persistent\MySQL\Builder\UpdateQueryBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UpdateQueryBuilderTest extends TestCase
{
    #[Test]
    public function selectClauseException(): void
    {
        $this->expectException(\LogicException::class);
        $sut = new UpdateQueryBuilder();
        $sut->addSelectClause('a1');
        $sut->getResult();
    }

    #[Test]
    public function firstResultException(): void
    {
        $this->expectException(\LogicException::class);
        $sut = new UpdateQueryBuilder();
        $sut->selectFirst();
        $sut->getResult();
    }

    #[Test]
    public function basicExample(): void
    {
        $sut = new UpdateQueryBuilder();
        $sut->setTable('table1');
        $sut->addInsertClause('a1', 'p1');
        $sut->addInsertClause('a2', 'p2');
        $sut->addWhereClause('param1', 'val1');
        $result = $sut->getResult();

        $this->assertEquals("UPDATE table1 SET a1 = 'p1', a2 = 'p2' WHERE param1 = 'val1';", $result);
    }
}