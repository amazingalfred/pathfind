<?php

declare(strict_types=1);

namespace App\Lib;

use App\Exceptions\GridSizeNotAllowedException;
use App\Exceptions\InconsistentGridStructureException;
use App\Exceptions\InvalidGridValueException;

use function is_bool;

/**
 * Builds adjacency lists from boolean matrix structured array
 */
class AdjacencyListBuilder
{
    private const INCLUDE_NON_TRAVERSABLE = true;
    private const MINIMUM_GRID_HEIGHT = 2;
    private const MINIMUM_GRID_WIDTH = 2;

    // Relative cell location vectors (row, col) representing up/down/left/right
    private const RELATIVE_LOCATIONS = [
        [-1, 0],
        [1, 0],
        [0, -1],
        [0, 1],
    ];

    private const TOTAL_DIRECTION_COUNT = 4;

    /**
     * Creates an adjacency list from a 2 dimensional grid.
     * Each node in the grid has is represented as a row.
     * Each of these rows is an array of integers representing the cell id of the adjacent reachable nodes
     *
     * @param array $grid 2 dimensional grid
     * @return array cell-id indexed array of grid nodes and their adjacent cell-ids
     */
    public function fromGrid(array $grid): array
    {
        $adjacencyList = [];

        [$rowCount, $rowWidth] = $this->getGridDimensions($grid);

        $nodeList = $this->flattenGrid($grid, $rowCount, $rowWidth);
        $nodeCount = count($nodeList);

        for ($nodeIndex = 0; $nodeIndex < $nodeCount; $nodeIndex++) {
            if (! $nodeList[$nodeIndex]['traversable'] && ! self::INCLUDE_NON_TRAVERSABLE) {
                continue;
            }

            $adjacentNodes = [];

            // Collect the adjacent, reachable cells
            for ($i = 0; $i < self::TOTAL_DIRECTION_COUNT; $i++) {
                // Don't look for adjacent nodes for non-traversable nodes
                if (! $nodeList[$nodeIndex]['traversable']) {
                    break;
                }

                $candidateRowIndex = $nodeList[$nodeIndex]['row'] + self::RELATIVE_LOCATIONS[$i][0];
                $candidateColIndex = $nodeList[$nodeIndex]['col'] + self::RELATIVE_LOCATIONS[$i][1];

                // Beyond lower bound
                if ($candidateRowIndex < 0 || $candidateColIndex < 0) {
                    continue;
                }

                // Beyond upper bound
                if ($candidateRowIndex >= $rowCount || $candidateColIndex >= $rowWidth) {
                    continue;
                }

                // Adjacent node is Non-traversable
                if ($grid[$candidateRowIndex][$candidateColIndex] === false) {
                    continue;
                }

                $cellIndex = $candidateRowIndex * $rowWidth + $candidateColIndex;

                $adjacentNodes[] = $cellIndex;
            }

            $adjacencyList[$nodeIndex] = $adjacentNodes;
        }

        return $adjacencyList;
    }

    /**
     * Returns dimensions of a valid grid
     *
     * @param array $grid 2 dimensional array representing a grid
     * @return array e.g. for 5 rows and 4 columns [5, 4]
     * @throws GridSizeNotAllowedException if the row or column count is below the minimum required
     */
    private function getGridDimensions(array $grid): array
    {
        $dimensionsSatisfied = false;

        $rowCount = count($grid);

        if ($rowCount >= self::MINIMUM_GRID_HEIGHT) {
            $dimensionsSatisfied = true;
        }

        $colCount = count($grid[0]);

        if ($colCount < self::MINIMUM_GRID_WIDTH) {
            $dimensionsSatisfied = false;
        }

        if (! $dimensionsSatisfied) {
            throw new GridSizeNotAllowedException(
                sprintf('Grid must be a minimum size of %d x %d.', self::MINIMUM_GRID_HEIGHT, self::MINIMUM_GRID_WIDTH)
            );
        }

        return [
            $rowCount,
            $colCount,
        ];
    }

    /**
     * Returns a grid as a simplified array of cell info
     *
     * @param array $grid
     * @param int $rowCount
     * @param $colCount
     * @return array
     */
    private function flattenGrid(array $grid, int $rowCount, $colCount): array
    {
        $cellList = [];

        for ($row = 0; $row < $rowCount; $row++) {
            $currentColCount = 0;

            if (count($grid[$row]) > $colCount) {
                throw new InconsistentGridStructureException('Grid must maintain a constant width for each row.');
            }

            for ($col = 0; $col < $colCount; $col++) {
                $currentColCount++;

                if (! array_key_exists($col, $grid[$row])) {
                    throw new InconsistentGridStructureException('Grid must maintain a constant width for each row.');
                }

                if (! is_bool($grid[$row][$col])) {
                    throw new InvalidGridValueException('Grid must contain boolean values only.');
                }

                $cellList[] = [
                    'row' => $row,
                    'col' => $col,
                    'traversable' => $grid[$row][$col],
                ];
            }
        }

        return $cellList;
    }
}
