<?php

declare(strict_types=1);

namespace Tests\Support;

use Exception;
use PHPUnit\Framework\TestCase;
use Tests\Support\Exceptions\MissingSymbolException;
use Tests\Support\Exceptions\UnexpectedSymbolException;

class MapConverterTest extends TestCase
{
    private MapConverter $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new MapConverter();
    }

    public function testItConvertsValidMaps(): void
    {
        $map = <<<EOF
            . P . . .
            . # # # .
            . . . . .
            . . Q . .
            . . . . .
EOF;

        $expectedStart = [0, 1];
        $expectedEnd = [3, 2];

        $expectedGrid = [
            [
                true,
                true,
                true,
                true,
                true,
            ],
            [
                true,
                false,
                false,
                false,
                true,
            ],
            [
                true,
                true,
                true,
                true,
                true,
            ],
            [
                true,
                true,
                true,
                true,
                true,
            ],
            [
                true,
                true,
                true,
                true,
                true,
            ],
        ];

        $actual = $this->sut->toMovableGrid($map);

        self::assertEquals(
            $expectedStart,
            $actual['start'],
            'Start position should be found.'
        );

        self::assertEquals(
            $expectedEnd,
            $actual['end'],
            'End position should be found.'
        );

        self::assertEquals(
            $expectedGrid,
            $actual['grid'],
            'Boolean grid should be generated with movable tiles marked as true, walls marked as false.'
        );
    }

    public function testItThrowsOnMissingStartSymbol(): void
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Map should include a start and end position.');

        $map = <<<EOL
            .....
            .....
            .....
EOL;

        $this->sut->toMovableGrid($map);
    }

    public function testItThrowsOnMissingEndSymbol(): void
    {
        self::expectException(MissingSymbolException::class);
        self::expectExceptionMessage('Map should include a start and end position.');

        $map = <<<EOL
            .P...
            .....
            .....
EOL;

        $this->sut->toMovableGrid($map);
    }

    public function testItThrowsOnUnknownSymbol(): void
    {
        self::expectException(UnexpectedSymbolException::class);
        self::expectExceptionMessage('Unknown symbol encountered. Maps may only contain the following "PQ#." and space.');

        $map = <<<EOL
            .P...
            .www.
            ..Q..
EOL;

        $this->sut->toMovableGrid($map);
    }

    public function testItProducesMapsWithInconsistentRowWithOnDemand(): void
    {
        $map = <<<EOL
            .P...
            .###.
            ..Q...
EOL;

        $actual = $this->sut->toMovableGrid($map);

        self::assertCount(3, $actual['grid'], 'Grid should have 3 rows.');

        self::assertCount(5, $actual['grid'][0], 'First row should have 5 rows.');
        self::assertCount(5, $actual['grid'][1], 'Second row should have 5 rows.');
        self::assertCount(6, $actual['grid'][2], 'Third row should have 6 rows.');
    }
}
