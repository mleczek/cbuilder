# Tests

## Packages

Following packages must be installed and registered in the system path:

- gcc/ar
- gdb

## Resources

### `temp`

Tests uses the `temp` directory to perform some checks on the real filesystem instead of using mocked `Filesystem` class. After each test this directory must be removed (which is done by default in `TestCase` class). As well this directory cannot exists before executing any test.

The `temp` directory is registered in Travis CI in `LD_LIBRARY_PATH` variable. It means that any executable file will search for shared libraries also in this directory.

### `resources/fixtures`

Directory contains common files required by the tests, eq. c++ programs.
