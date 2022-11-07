<?php

declare(strict_types=1);

namespace App;

use App\Exceptions\InputException;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Finds the shortest number of steps between two points on a grid
 *
 * @param array $grid 2-dimensional array of boolean values, with false representing an in-passable block
 * @param array $startVec an array containing the row and column index of the start position
 * @param array $endVec an array containing the row and column index of the end position
 * @return int the number of steps, or -1 if no path was found
 * @throws InputException
 */
function pathfind(array $grid, array $startVec, array $endVec): int
{
    $pathFinder = new PathFinder();

    return $pathFinder->pathFind($grid, $startVec, $endVec);
}
