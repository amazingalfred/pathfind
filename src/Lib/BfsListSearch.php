<?php

declare(strict_types=1);

namespace App\Lib;

use Exception;
use App\Exceptions\LocationOutOfBoundsException;

/**
 * Performs a breath first search over an adjacency list
 */
class BfsListSearch
{
    /* @var array<int, int> Queue of cell-ids to check for destination or adjacent nodes */
    private array $queue;

    /* @var array<int, int> cell-id, cell-id Tracks the parents of each node discovered to allow backtracking */
    // private array $parents;

    /** @var array<int, bool> Indexed by cell-id; Tracks which nodes have been visited to prevent duplicate work */
    private array $visited;

    /** @var int used to track when the depth of the search increments */
    private int $queuedAtNextLevel = 0;

    /**
     * Get the shortest number of moves to reach the destination from the source
     *
     * @param array $adjacencyList
     * @param int $sourceCell zero-based cell id of the cell to start the search from
     * @param int $destinationCell zero-based cell id of the cell to search for
     * @return int Returns -1 if the destination cannot be reached
     * @throws Exception
     */
    public function shortestPath(array $adjacencyList, int $sourceCell, int $destinationCell): int
    {
        if ($sourceCell === $destinationCell) {
            throw new LocationOutOfBoundsException('Source and destination must not be the same.');
        }

        if (! array_key_exists($sourceCell, $adjacencyList)) {
            throw new LocationOutOfBoundsException('Source must be a valid grid location.');
        }

        if (! array_key_exists($destinationCell, $adjacencyList)) {
            throw new LocationOutOfBoundsException('Destination must be a valid grid location.');
        }

        $this->visited = array_fill(0, count($adjacencyList), false);
        $found = false;

        // Initialise a search queue, starting at the source
        $this->queue = [$sourceCell];

        // Track progress to the next depth level in the search
        $queuedAtThisLevel = 1;
        $this->queuedAtNextLevel = 0;
        $depthLevel = 0;

        // Queued nodes are marked as visited to prevent duplicate processing
        $this->visited[$sourceCell] = true;

        // Registering the parents allows the routes to be collected
        $this->parents = array_fill(0, count($adjacencyList), null);

        // Mark the source as its own parent
        $this->parents[$sourceCell] = $sourceCell;

        // Check the queue size on each loop
        while (count($this->queue) > 0) {
            $node = array_shift($this->queue);
            $queuedAtThisLevel--;

            if ($node === $destinationCell) {
                $found = true;

                break;
            }

            $this->queueAdjacent($adjacencyList, $node);

            if ($queuedAtThisLevel === 0) {
                $queuedAtThisLevel = $this->queuedAtNextLevel;
                $this->queuedAtNextLevel = 0;
                $depthLevel++;
            }
        }

        if ($found) {
            // Debug Only
            // var_export($this->traverseBackQueue($this->parents, $destinationCell, $sourceCell, $depthLevel));

            return $depthLevel;
        }

        return -1;
    }

    /**
     * Add any adjacent cell indexes to the queue
     *
     * @param array $adjacencyList
     * @param int $cellIndex
     * @return void
     */
    private function queueAdjacent(array $adjacencyList, int $cellIndex): void
    {
        $adjacent =  $adjacencyList[$cellIndex];

        foreach ($adjacent as $neighbour) {
            if ($this->visited[$neighbour]) {
                continue;
            }

            $this->visited[$neighbour] = true;
            $this->queue[] = $neighbour;
            // $this->parents[$neighbour] = $cellIndex;
            $this->queuedAtNextLevel++;
        }
    }

    /**
     * Returns the path, as an array of cell references, by following the path member parent references back to
     * the starting point.
     *
     * @param array $backQueue
     * @param int $lastIndex
     * @param int $sourceCell
     * @param int $stepCount
     * @return int[]
     */
    /*
    private function traverseBackQueue(array $backQueue, int $lastIndex, int $sourceCell, int $stepCount): array
    {
        $path = [$lastIndex];

        $prev = $backQueue[$lastIndex];

        for ($i = $stepCount; $i > 0; $i--) {
            array_unshift($path, $prev);

            $prev = $backQueue[$prev];

            if ($prev === null) {
                throw new RuntimeException('Encountered unset path member whilst back tracking path.');
            }

            if ($prev === $sourceCell) {
                break;
            }
        }

        return $path;
    }
    */
}
