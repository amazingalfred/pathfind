<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\GridSizeNotAllowedException;
use App\Exceptions\InconsistentGridStructureException;
use App\Exceptions\InputException;
use App\Exceptions\InvalidGridValueException;
use App\Lib\AdjacencyListBuilder;
use App\Lib\BfsListSearch;
use App\PathFinder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PathFinderTest extends TestCase
{
    private AdjacencyListBuilder|MockObject $mockAdjacencyListBuilder;
    private BfsListSearch|MockObject $mockBfsListSearch;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockAdjacencyListBuilder = $this->createMock(AdjacencyListBuilder::class);
        $this->mockBfsListSearch = $this->createMock(BfsListSearch::class);

        $this->sut = new PathFinder(
            $this->mockAdjacencyListBuilder,
            $this->mockBfsListSearch
        );
    }

    public function testItValidatesTheGridSize(): void
    {
        self::expectException(InputException::class);
        self::expectExceptionMessage('The first argument must be a two dimensional grid, at least 2x2 in size.');

        $this->mockAdjacencyListBuilder->method('fromGrid')
            ->willThrowException(new GridSizeNotAllowedException());

        $grid = [
            [true],
            [true]
        ];

        $this->sut->pathFind($grid, [0, 0], [1, 0]);
    }

    public function testItValidatesTheGridWidthConsistency(): void
    {
        self::expectException(InputException::class);
        self::expectExceptionMessage('There should be an equal number of columns in each row of the grid.');

        $this->mockAdjacencyListBuilder->method('fromGrid')
            ->willThrowException(new InconsistentGridStructureException());

        $grid = [
            [true],
            [true]
        ];

        $this->sut->pathFind($grid, [0, 0], [1, 0]);
    }

    public function testItValidatesTheStartPositionIsInBounds(): void
    {
        self::expectException(InputException::class);
        self::expectExceptionMessage('Start vector must be a valid grid location.');

        $grid = [
            [true, true],
            [true, true]
        ];

        $this->sut->pathFind($grid, [3, 0], [1, 0]);
    }

    public function testItValidatesTheStartPositionIsNotBlocked(): void
    {
        self::expectException(InputException::class);
        self::expectExceptionMessage('Start vector must be a valid grid location.');

        $grid = [
            [false, true],
            [true, true]
        ];

        $this->sut->pathFind($grid, [0, 0], [1, 0]);
    }

    public function testItValidatesTheEndPositionIsInBounds(): void
    {
        self::expectException(InputException::class);
        self::expectExceptionMessage('End vector must be a valid grid location.');

        $grid = [
            [true, true],
            [true, true]
        ];

        $this->sut->pathFind($grid, [0, 0], [3, 0]);
    }

    public function testItValidatesTheEndPositionIsNotBlocked(): void
    {
        self::expectException(InputException::class);
        self::expectExceptionMessage('End vector must be a valid grid location.');

        $grid = [
            [true, true],
            [true, false]
        ];

        $this->sut->pathFind($grid, [0, 0], [1, 1]);
    }

    public function testItValidatesTheGridValues(): void
    {
        self::expectException(InputException::class);
        self::expectExceptionMessage('Each value in the grid must be of boolean type.');

        $this->mockAdjacencyListBuilder->method('fromGrid')
            ->willThrowException(new InvalidGridValueException());

        $grid = [
            [true, true],
            [true, 'true']
        ];

        $this->sut->pathFind($grid, [0, 0], [1, 1]);
    }

    public function testItReturnsTheNumberOfStepsIfFound(): void
    {
        $grid = [
            [true, true, true],
            [true, true, true],
            [true, true, true],
        ];

        $this->mockAdjacencyListBuilder->method('fromGrid')
            ->willReturn([]);

        $this->mockBfsListSearch->method('shortestPath')
            ->willReturn(4);

        $expectedSteps = 4;
        $actualSteps = $this->sut->pathFind($grid, [0, 0], [2, 2]);

        self::assertEquals($expectedSteps, $actualSteps, 'Actual steps must match expected steps.');
    }

    public function testItReturnsNegativeOneIfNotFound(): void
    {
        $grid = [
            [true, true, true],
            [false, false, false],
            [true, true, true],
        ];

        $this->mockAdjacencyListBuilder->method('fromGrid')
            ->willReturn([]);

        $this->mockBfsListSearch->method('shortestPath')
            ->willReturn(-1);


        $expectedOutput = -1;
        $actualOutput = $this->sut->pathFind($grid, [0, 0], [2, 2]);

        self::assertEquals($expectedOutput, $actualOutput, 'Actual steps must match expected steps.');
    }
}
