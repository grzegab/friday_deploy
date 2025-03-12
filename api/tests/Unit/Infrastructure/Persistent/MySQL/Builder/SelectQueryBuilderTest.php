<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Persistent\MySQL\Builder;

use App\Infrastructure\Persistent\MySQL\Builder\RemoveQueryBuilder;
use App\Infrastructure\Persistent\MySQL\Builder\SelectQueryBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SelectQueryBuilderTest extends TestCase
{
    #[Test]
    public function insertClauseException(): void
    {
        $this->expectException(\LogicException::class);
        $sut = new SelectQueryBuilder();
        $sut->addInsertClause('p1', 'v1');
        $sut->getResult();
    }

    #[Test]
    public function basicExample(): void
    {
        $sut = new SelectQueryBuilder();
        $sut->setTable('table1');
        $sut->addSelectClause('a1');
        $sut->addWhereClause('param1', 'val1');
        $result = $sut->getResult();

        $this->assertEquals("SELECT a1 FROM table1 WHERE param1 = 'val1';", $result);
    }

    #[Test]
    public function aliasExample(): void
    {
        $sut = new SelectQueryBuilder();
        $sut->setTable('table1');
        $sut->addSelectClause('a1', 'alias1');
        $sut->addWhereClause('param1', 'val1');
        $result = $sut->getResult();

        $this->assertEquals("SELECT a1 AS alias1 FROM table1 WHERE param1 = 'val1';", $result);
    }

    #[Test]
    public function dotExample(): void
    {
        $sut = new SelectQueryBuilder();
        $sut->setTable('table1');
        $sut->addSelectClause('something.a1');
        $sut->addWhereClause('param1', 'val1');
        $result = $sut->getResult();

        $this->assertEquals("SELECT something.a1 AS a1 FROM table1 WHERE param1 = 'val1';", $result);
    }

    #[Test]
    public function joinExample(): void
    {
        $sut = new SelectQueryBuilder();
        $sut->setTable('table1');
        $sut->joinTable('table2', 'table1.id', 'table1_id');
        $sut->addSelectClause('something.a1');
        $sut->addWhereClause('param1', 'val1');
        $result = $sut->getResult();
        $expected = "SELECT something.a1 AS a1 FROM table1 LEFT JOIN table2 ON table2.table1.id = table1_id WHERE param1 = 'val1';";

        $this->assertEquals($expected, $result);
    }
}