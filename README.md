# Path Finding Solution

Solution uses the Breath First Search algorithm to find the shortest number of steps from the source to the destination.

A pathfind function is provided, the function requires a grid with a minimum size of 2x2, both the start and end vector
values must be valid grid positions. The minimum number of moves will be returned, or -1 to indicate no path was found.

The implementation for this challenge is focused on readability. The input grid is converted to an adjacency list to 
simplify the code. Performance could be improved by finding adjacent nodes on the fly during the BFS search, as  
only visited nodes would be checked.  

The spec called for a function as the interface, a facade has been provided for this reason. The actual implementation  
is encapsulated into classes.

# Install Dependencies (Dev)

`$ docker compose run --rm composer`

# Run Demo

`$ docker compose run --rm composer demo`

# Run Tests

`$ docker compose run --rm composer test`

## What I'm Pleased With
 - The code satisfies the specified functionality.
 - The code adheres to PSR-12 coding style.
 - Unit tests cover every unit of code and maintain coding standards.
 - Descriptive naming is used to ensure the code is easy to follow for a wide audience.
 - Functional testing is included to provide assurance the solution works as a whole
 - A test helper is used in the functional tests to allow use of text based maps to improve readability 

## What I Would Have Done With More Time
 1) Make the path discovered available rather than just the steps. Code to produce the path has been included to show how. 
    As this function was not requested, no time has been spent exposing the functionality/covering with tests.

 2) Either refactor findPath as a facade or pass in a service locator for AdjacencyList & BfsListSearch to allow
    isolated unit testing of the findPath.

 3) Review implementation for performance with large inputs, if needed, optimise and/or set upper bound on input & document.  
    Compare performance of a functional approach.

 4) Test efficiency of parallelized BFS in PHP.

 5) Generate text-based, graphic output showing the shortest path on grid.

 6) Allow non-uniform map shapes (remove requirement for equal width on every row).
    Currently, the size of the first row is used to determine the width of the map. 
 
 7) Make movable dimensions dynamic and offer diagonal and/or 3D movement.  
    This would increase in complexity of any cropping logic.