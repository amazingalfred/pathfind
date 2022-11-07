<?php

declare(strict_types=1);

namespace Tests;

use App\Lib\BfsListSearch;
use PHPUnit\Framework\TestCase;
use App\Exceptions\LocationOutOfBoundsException;

class BfsListSearchTest extends TestCase
{
    private BfsListSearch $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new BfsListSearch();
    }

    public function testItFindsTheFewestNumberOfSteps(): void
    {
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

        $sampleAdjacencyList = [
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

        $expected = 4;

        $actual = $this->sut->shortestPath($sampleAdjacencyList, 1, 9);

        self::assertEquals($expected, $actual, 'Expected path not taken in BFS.');
    }

    public function testItReturnsNegativeOneIfTheDestinationCannotBeReached()
    {
        $sampleAdjacencyList = [
            // Row One
            0 => [4, 1],
            1 => [0, 2],
            2 => [1, 3],
            3 => [7, 2],
            // Row Two
            4 => [],
            5 => [],
            6 => [],
            7 => [],
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

        $expected = -1;

        $actual = $this->sut->shortestPath($sampleAdjacencyList, 1, 9);

        self::assertEquals($expected, $actual, 'Method should return -1 if the destination is unreachable.');
    }

    public function testItThrowsOnOutOfBoundSource()
    {
        self::expectException(LocationOutOfBoundsException::class);
        self::expectExceptionMessage('Source must be a valid grid location.');

        $sampleAdjacencyList = [
            // Row One
            0 => [4, 1],
            1 => [0, 2],
            2 => [1, 3],
            3 => [7, 2],
            // Row Two
            4 => [],
            5 => [],
            6 => [],
            7 => [],
            // Row Three
            8 => [4, 12, 9],
            9 => [13, 8, 10],
            10 => [14, 9, 11],
            11 => [7, 15, 10],
        ];

        $this->sut->shortestPath($sampleAdjacencyList, 18, 9);
    }

    public function testItThrowsOnMatchingSourceAndDestination()
    {
        self::expectException(LocationOutOfBoundsException::class);
        self::expectExceptionMessage('Source and destination must not be the same.');

        $sampleAdjacencyList = [
            // Row One
            0 => [4, 1],
            1 => [0, 2],
            2 => [1, 3],
            3 => [7, 2],
            // Row Two
            4 => [],
            5 => [],
            6 => [],
            7 => [],
            // Row Three
            8 => [4, 12, 9],
            9 => [13, 8, 10],
            10 => [14, 9, 11],
            11 => [7, 15, 10],
        ];

        $this->sut->shortestPath($sampleAdjacencyList, 1, 1);
    }

    public function testItThrowsOnOutOfBoundDestination()
    {
        self::expectException(LocationOutOfBoundsException::class);
        self::expectExceptionMessage('Destination must be a valid grid location.');

        $sampleAdjacencyList = [
            // Row One
            0 => [4, 1],
            1 => [0, 2],
            2 => [1, 3],
            3 => [7, 2],
            // Row Two
            4 => [],
            5 => [],
            6 => [],
            7 => [],
            // Row Three
            8 => [4, 12, 9],
            9 => [13, 8, 10],
            10 => [14, 9, 11],
            11 => [7, 15, 10],
        ];

        $this->sut->shortestPath($sampleAdjacencyList, 1, 18);
    }
}
