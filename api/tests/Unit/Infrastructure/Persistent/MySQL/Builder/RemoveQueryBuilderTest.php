<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Persistent\MySQL\Builder;

use App\Infrastructure\Persistent\MySQL\Builder\RemoveQueryBuilder;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RemoveQueryBuilderTest extends TestCase
{
    #[Test]
    public function selectClauseException(): void
    {
        $this->expectException(\LogicException::class);
        $sut = new RemoveQueryBuilder();
        $sut->addSelectClause('a1');
        $sut->getResult();
    }

    #[Test]
    public function insertClauseException(): void
    {
        $this->expectException(\LogicException::class);
        $sut = new RemoveQueryBuilder();
        $sut->addInsertClause('p1', 'v1');
        $sut->getResult();
    }

    #[Test]
    public function basicExample(): void
    {
        $sut = new RemoveQueryBuilder();
        $sut->setTable('table1');
        $sut->addWhereClause('param1', 'val1');
        $result = $sut->getResult();

        $this->assertEquals("DELETE FROM table1 WHERE param1 = 'val1';", $result);
    }

    #[Test]
    public function emptyWhereException(): void
    {
        $this->expectException(\RuntimeException::class);
        $sut = new RemoveQueryBuilder();
        $sut->setTable('table1');
        $sut->getResult();
    }
}