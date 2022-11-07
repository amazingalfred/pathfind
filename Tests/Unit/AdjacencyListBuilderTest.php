<?php

declare(strict_types=1);

namespace Tests;

use App\Exceptions\GridSizeNotAllowedException;
use App\Exceptions\InconsistentGridStructureException;
use App\Exceptions\InvalidGridValueException;
use App\Lib\AdjacencyListBuilder;
use PHPUnit\Framework\TestCase;

class AdjacencyListBuilderTest extends TestCase
{
    /**
     * 4x5 Grid of spaces with a boolean value representing whether the space can be traversed
     */
    private const GRID_SAMPLE = [
        [true, true, true, true],
        [true, false, false, true],
        [true, true, true, true],
        [true, true, true, true],
        [true, true, true, true],
    ];

    /**
     *  List of nodes from 4x5 grid each listing their reachable neighbours.
     *
     *  Uses grid-cell references, numbered left-to-right, top-to-bottom.
     *    Example structure:
     *      |__0_|__1_|__2_|__3_|
     *      |__4_|__5_|__6_|__7_|
     *      |__8_|__9_|_10_|_11_|
     *      |_12_|_13_|_14_|_15_|
     *      |_16_|_17_|_18_|_19_|
     */
    private const ADJACENCY_LIST_SAMPLE = [
        // Row One
        0 => [4, 1],
        1 => [0, 2],
        2 => [1, 3],
        3 => [7, 2],
        // Row Two
        4 => [0, 8],
        5 => [],
        6 => [],
        7 => [3, 11],
        // Row Three
        8 => [4, 12, 9],
        9 => [13, 8, 10],
        10 => [14, 9, 11],
        11 => [7, 15, 10],
        // Row Four
        12 => [8, 16, 13],
        13 => [9, 17, 12, 14],
        14 => [10, 18, 13, 15],
        15 => [11, 19, 14],
        // Row Five
        16 => [12, 17],
        17 => [13, 16, 18],
        18 => [14, 17, 19],
        19 => [15, 18],
    ];

    private AdjacencyListBuilder $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new AdjacencyListBuilder();
    }

    public function testListIsProducedFromGrid(): void
    {
        $expectedNodeCount = 20;

        $actual = $this->sut->fromGrid(self::GRID_SAMPLE);

        self::assertCount(
            $expectedNodeCount,
            $actual,
            'There should be exactly ' . $expectedNodeCount . ' nodes in the produced adjacency list.'
        );

        self::assertEquals(
            self::ADJACENCY_LIST_SAMPLE,
            $actual,
            'The adjacency list produced does not match the expected output.'
        );
    }

    public function testItThrowsOnNonBooleanGridValue(): void
    {
        self::expectException(InvalidGridValueException::class);
        self::expectExceptionMessage('Grid must contain boolean values only.');

        $this->sut->fromGrid([
            [true, true, true],
            [true, 'true'],
            [true, true, true],
        ]);
    }

    public function testItThrowsOnSubsequentRowsSmallerThanFirst(): void
    {
        self::expectException(InconsistentGridStructureException::class);
        self::expectExceptionMessage('Grid must maintain a constant width for each row.');

        $this->sut->fromGrid([
            [true, true, true],
            [true, true],
            [true, true, true],
        ]);
    }

    public function testItThrowsOnSubsequentRowsLargerThanFirst(): void
    {
        self::expectException(InconsistentGridStructureException::class);
        self::expectExceptionMessage('Grid must maintain a constant width for each row.');

        $this->sut->fromGrid([
            [true, true],
            [true, true, true],
            [true, true, true],
        ]);
    }


    public function testItThrowsOnGridsSmallerThan2By2(): void
    {
        self::expectException(GridSizeNotAllowedException::class);
        self::expectExceptionMessage('Grid must be a minimum size of 2 x 2.');

        $this->sut->fromGrid([
            [true],
            [true],
            [true],
        ]);
    }
}
