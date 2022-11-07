<?php

require_once __DIR__ . '/../src/pathfind.php';

use function App\pathfind;

$a = [
    [true, true, true, true, true],
    [true, false, false, false, true],
    [true, true, true, true, true],
    [true, true, true, true, true],
    [true, true, true, true, true],
];

$p = [0, 1];

$q = [3, 2];

$result = pathfind($a, $p, [3, 2]);

if ($result === -1) {
    echo 'Unable to find a path';

    return;
}

echo 'Shortest path is ' . $result . ' moves.' . PHP_EOL;