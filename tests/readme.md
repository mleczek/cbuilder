# Tests

## `temp` directory

Tests uses the `temp` directory to perform some checks on the real filesystem instead of using mocked `Filesystem` class. After each test this directory must be removed (which is done by default in `TestCase` class). As well this directory cannot exists before executing any test.
