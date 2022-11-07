<?php

declare(strict_types=1);

namespace Tests\Support;

use Exception;
use Tests\Support\Exceptions\MissingSymbolException;
use Tests\Support\Exceptions\UnexpectedSymbolException;

/**
 * This test helper converts string based map challenges into the boolean matrix required by the pathfinder.
 *
 * This allows tests to be written using the string based map format, which is often easier to parse for humans.
 */
class MapConverter
{
    /**
     * Accepts a string map, using "." to represent a traversable tile.
     * "P" & "Q" represent a traversable tile, that is marked, respectively,
     * as the start and end positions. "W" represents a wall/immovable space.
     *
     * As this is a test helper, maps results that don't satisfy the grid size rules may be created.
     *
     * @param string $map A string based map at least 2x2 in size; Must contain start and end positions.
     * @throws Exception On invalid map input
     */
    public function toMovableGrid(string $map): array
    {
        $map = str_replace(' ', '', $map);
        $mapLines = explode("\n", $map);

        $mapHeight = count($mapLines);

        $startVec =  null;
        $endVec = null;

        $grid = array_fill(0, $mapHeight, []);

        foreach ($mapLines as $rowIndex => $line) {
            for ($columnIndex = 0; $columnIndex < strlen($line); $columnIndex++) {
                switch ($line[$columnIndex]) {
                    case '#':
                        $grid[$rowIndex][$columnIndex] = false;

                        continue 2;
                    case 'P':
                        if ($startVec !== null) {
                            throw new UnexpectedSymbolException('Maps should only contain one start symbol.');
                        }

                        $startVec = [$rowIndex, $columnIndex];

                        break;
                    case 'Q':
                        if ($endVec !== null) {
                            throw new UnexpectedSymbolException('Maps should only contain one end symbol.');
                        }

                        $endVec = [$rowIndex, $columnIndex];

                        break;
                    case '.':
                        // no-op

                        break;
                    default:
                        throw new UnexpectedSymbolException(
                            'Unknown symbol encountered. Maps may only contain the following "PQ#." and space.'
                        );
                }

                $grid[$rowIndex][$columnIndex] = true;
            }
        }

        if ($startVec === null || $endVec === null) {
            throw new MissingSymbolException('Map should include a start and end position.');
        }

        return [
            'start' => $startVec,
            'end' => $endVec,
            'grid' => $grid,
        ];
    }
}
