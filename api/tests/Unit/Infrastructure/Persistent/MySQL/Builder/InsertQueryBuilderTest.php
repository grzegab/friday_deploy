<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Persistent\MySQL\Builder;

use App\Infrastructure\Persistent\MySQL\Builder\InsertQueryBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class InsertQueryBuilderTest extends TestCase
{
    #[Test]
    public function selectClauseException(): void
    {
        $this->expectException(\LogicException::class);
        $sut = new InsertQueryBuilder();
        $sut->addSelectClause('a1');
        $sut->getResult();
    }

    #[Test]
    public function firstResultException(): void
    {
        $this->expectException(\LogicException::class);
        $sut = new InsertQueryBuilder();
        $sut->selectFirst();
        $sut->getResult();
    }

    #[Test]
    public function basicExample(): void
    {
        $sut = new InsertQueryBuilder();
        $sut->setTable('table1');
        $sut->addInsertClause('insert1', 'value1');
        $sut->addInsertClause('insert2', 'value2');
        $result = $sut->getResult();

        $this->assertEquals("INSERT INTO table1 (`insert1`, `insert2`) VALUES ('value1', 'value2')", $result);
    }
}