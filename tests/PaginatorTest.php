<?php

namespace Leo980\Paginator\Tests;

use Leo980\Paginator\Paginator;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Leo\Paginator\Paginator
 */
class PaginatorTest extends TestCase
{
    private Paginator $p;

    public function setUp(): void
    {
        $this->p = new Paginator(2);
    }

    /**
     * @testdox __construct(): Reject negative $neighbours parameter
     */
    public function testRejectNegativeNeighbours(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/^\$neighbours /');
        new Paginator(-1);
    }

    /**
     * @testdox __invoke(): Reject negative $pages
     */
    public function testRejectNegativePages(): void
    {
        $this->expectException(\RangeException::class);
        $this->expectExceptionMessageMatches('/^\$pages /');
        ($this->p)(1, -1);
    }

    /**
     * @testdox __invoke(): Reject $page >= $pages
     * @depends Leo980\Paginator\Tests\PaginatorTest::testPagesIs0
     * @depends Leo980\Paginator\Tests\PaginatorTest::testPagesIs1
     */
    public function testRejectPageGreaterThanPages(): void
    {
        $this->expectException(\RangeException::class);
        $this->expectExceptionMessageMatches('/^\$page /');
        ($this->p)(100, 10);
    }

    /**
     * @testdox __invoke(): $page == 0
     */
    public function testPagesIs0(): void
    {
        $this->assertSame([], ($this->p)(0, 0));
    }

    /**
     * @testdox __invoke(): $page == 1
     */
    public function testPagesIs1(): void
    {
        $this->assertSame([], ($this->p)(1, 1));
    }

    public function testGenerateCurrentPageWithNeighbours(): void
    {
        $this->assertSame(
            [1, null, 48, 49, 50, 51, 52, null, 100],
            ($this->p)(50, 100),
        );
    }

    public function testRemoveRedundantCollapsedPageIndicator(): void
    {
        $this->assertSame(
            [1, 2, 3, 4, 5, 6, null, 20],
            ($this->p)(4, 20),
        );
    }

    public function testReplaceRedundantCollapsedPageIndicator(): void
    {
        $this->assertSame(
            [1, 2, 3, 4, 5, 6, 7, null, 20],
            ($this->p)(5, 20),
        );
    }
}