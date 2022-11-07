<?php

declare(strict_types=1);

namespace App;

use App\Exceptions\GridSizeNotAllowedException;
use App\Exceptions\InconsistentGridStructureException;
use App\Exceptions\InputException;
use App\Exceptions\InvalidGridValueException;
use App\Lib\AdjacencyListBuilder;
use App\Lib\BfsListSearch;

class PathFinder
{
    private AdjacencyListBuilder $adjacencyListBuilder;
    private BfsListSearch $bfsListSearch;

    public function __construct(
        ?AdjacencyListBuilder $adjacencyListBuilder = null,
        ?BfsListSearch $bfsListSearch = null
    ) {
        $this->adjacencyListBuilder = $adjacencyListBuilder ?? new AdjacencyListBuilder();
        $this->bfsListSearch = $bfsListSearch ?? new BfsListSearch();
    }

    public function pathFind(array $grid, array $startVec, array $endVec): int
    {
        if (! $this->validateVector($grid, $startVec)) {
            throw new InputException('Start vector must be a valid grid location.');
        }

        if (! $this->validateVector($grid, $endVec)) {
            throw new InputException('End vector must be a valid grid location.');
        }

        $gridWidth = count($grid[0]);

        $sourceCellId = $startVec[0] * $gridWidth + $startVec[1];
        $destinationCellId = $endVec[0] * $gridWidth + $endVec[1];

        try {
            $adjacencyList = $this->adjacencyListBuilder->fromGrid($grid);

            $shortestPath = $this->bfsListSearch->shortestPath($adjacencyList, $sourceCellId, $destinationCellId);
        } catch (GridSizeNotAllowedException $exception) {
            throw new InputException('The first argument must be a two dimensional grid, at least 2x2 in size.');
        } catch (InconsistentGridStructureException $exception) {
            throw new InputException('There should be an equal number of columns in each row of the grid.');
        } catch (InvalidGridValueException $exception) {
            throw new InputException('Each value in the grid must be of boolean type.');
        }

        return $shortestPath;
    }

    /**
     * Checks the vector exists in the provided grid.
     */
    private function validateVector(array $grid, array $vec): bool
    {
        if (
            ! array_key_exists($vec[0], $grid)
            || ! array_key_exists($vec[1], $grid[$vec[0]])
            || $grid[$vec[0]][$vec[1]] === false
        ) {
            return false;
        }

        return true;
    }
}
