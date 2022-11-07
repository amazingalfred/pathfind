<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Exceptions\InputException;
use PHPUnit\Framework\TestCase;
use Tests\Support\MapConverter;

use function App\pathfind;

class PathFindTest extends TestCase
{
    private MapConverter $mapConverter;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->mapConverter = new MapConverter();

        require_once __DIR__ . '/../../src/pathfind.php';
    }

    public function testPathFindTakesTheExpectedNumberOfSteps()
    {
        $grid = [
            [true, true, true, true, true],
            [true, false, false, false, true],
            [true, true, true, true, true],
            [true, true, true, true, true],
            [true, true, true, true, true],
        ];

        $expectedMoveCount = 6;
        $actualMoveCount = pathfind($grid, [0, 1], [3, 2]);

        self::assertEquals(
            $expectedMoveCount,
            $actualMoveCount,
            'Path taken should match the expected number of moves.'
        );
    }

    public function testItThrowsOnOutOfBoundsSource(): void
    {
        $this->expectException(InputException::class);

        $grid = [
            [true, true, true, true, true],
            [true, false, false, false, true],
            [true, true, true, true, true],
            [true, true, true, true, true],
            [true, true, true, true, true],
        ];

        $expectedMoveCount = 6;
        $actualMoveCount = pathfind($grid, [0, 22], [3, 2]);

        self::assertEquals(
            $expectedMoveCount,
            $actualMoveCount,
            'Path taken should match the expected number of moves.'
        );
    }

    public function testItThrowsOnOutOfBoundsDestination(): void
    {
        $this->expectException(InputException::class);

        $grid = [
            [true, true, true, true, true],
            [true, false, false, false, true],
            [true, true, true, true, true],
            [true, true, true, true, true],
            [true, true, true, true, true],
        ];

        $expectedMoveCount = 6;
        $actualMoveCount = pathfind($grid, [0, 1], [7, 2]);

        self::assertEquals(
            $expectedMoveCount,
            $actualMoveCount,
            'Path taken should match the expected number of moves.'
        );
    }

    /**
     * @dataProvider provideGoodMap
     */
    public function testGoodMapsAreSolvedInTheExpectedNumberOfMoves(string $map, int $expectedPathSize): void
    {
        $convertedMap = $this->mapConverter->toMovableGrid($map);

        self::assertEquals($expectedPathSize, pathfind(
            $convertedMap['grid'],
            $convertedMap['start'],
            $convertedMap['end']
        ));
    }

    /**
     * @dataProvider provideBadMap
     */
    public function testBadMapsGiveTheExpectedResponse(string $map, int $expectedPathSize): void
    {
        $convertedMap = $this->mapConverter->toMovableGrid($map);

        self::assertEquals(
            $expectedPathSize,
            pathfind(
                $convertedMap['grid'],
                $convertedMap['start'],
                $convertedMap['end']
            )
        );
    }

    /**
     * @dataProvider provideInvalidMap
     */
    public function testInvalidMapsGiveTheExpectedResponse(string $map, string $expectedErrorMessage): void
    {
        self::expectException(InputException::class);
        self::expectExceptionMessage($expectedErrorMessage);

        $convertedMap = $this->mapConverter->toMovableGrid($map);

        pathfind(
            $convertedMap['grid'],
            $convertedMap['start'],
            $convertedMap['end']
        );
    }

    public function provideGoodMap(): array
    {
        $map1 = <<<EOL
            . P . . .
            . # # # .
            . . . . .
            . . Q . .
            . . . . .
        EOL;

        $map2 = <<<EOL
            . P . . .
            # # # # .
            . . . . .
            . . Q . .
            . . . . .
        EOL;

        $map3 = <<<EOL
            . P . . .
            . . . . .
            . . . . .
            . . Q . .
            . . . . .
        EOL;

        $map4 = <<<EOL
            . P . . . . . . . .
            . . . . . . . . . .
            . . . . . . . . . .
            . . . . . . . . . .
            . . . . . . . . . .
            . . . . . . . . . .
            . . . . . . . . . .
            . # # # # # # . . .
            . . Q . . . . . . .
            . . . . . . . . . .
        EOL;

        $map5 = <<<EOL
            . P . . . . . . . .
            . . . . . . . . . .
            . . . . . . . . . .
            . . . . . . . . . .
            . . . . . . . . . .
            . . . . . . . . . .
            . . . . . . . . . .
            . # # # # # # . . .
            . . . . . . . . . .
            . . . . . . . . . .
            . . . . . # # # # #
            . . . . . . . . . .
            # # # # # # . . . .
            . . . . . . . . . .
            . . . . . . . . . .
            . . . . . . . . . .
            . . . . . . . . . .
            . # # # # # # . . .
            . . Q . . . # . . .
            . . . . . . # . . .
        EOL;

         return [
             [$map1, 6],
             [$map2, 8],
             [$map3, 4],
             [$map4, 11],
             [$map5, 33],
         ];
    }

    public function provideBadMap(): array
    {
        $map1 = <<<EOL
            . P . . .
            # # # # #
            . . . . .
            . . Q . .
            . . . . .
        EOL;

        $map2 = <<<EOL
            # # # . .
            # P # . .
            # # # . .
            . . Q . .
            . . . . .
        EOL;

        return [
            [$map1, -1],
            [$map2, -1],
        ];
    }

    public function provideInvalidMap(): array
    {
        $map1 = <<<EOL
            . P . Q .
        EOL;

        $map2 = <<<EOL
            . P . . .
            . . . .
            . . Q . .
        EOL;

        return [
            [$map1, 'The first argument must be a two dimensional grid, at least 2x2 in size.'],
            [$map2, 'There should be an equal number of columns in each row of the grid.'],
        ];
    }
}
