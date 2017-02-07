# Command Line Interface

## Build

Run compilation and linking process and save produced artifacts.

```sh
cbuilder build [--debug] [--compiler=gcc] [--module=<package>]
```

## Clean

Remove artifacts produced by the build command.

```sh
cbuilder clean [--module=<package>]
```

## Conflicts

Shows which packages prevent the given package from being installed.

```sh
cbuilder conflicts <package>[:<version>]
```

## Depends

Shows which packages cause the given package to be installed.

```sh
cbuilder depends <package>
```

## Install

Installs and build the project dependencies from the cbuilder.lock file if present, or falls back on the cbuilder.json.

```sh
cbuilder install
```

## Init

Creates a basic cbuilder.json file in current directory.

```sh
cbuilder init
```

## Outdated

Shows a list of installed packages that have updates available, including their latest version.

```sh
cbuilder outdated
```

## Rebuild

Alias for clean and build command.

```sh
cbuilder rebuild [--debug] [--compiler=gcc] [--module=<package>]
```

## Remove

Removes a package from the require or require-dev.

```sh
cbuilder remove <package>
```

## Require

Adds required packages to your cbuilder.json, installs and builds them.

```sh
cbuilder require <package>[:<version>]... [--dev]
```

## Run

Run package or the scripts defined in the cbuilder.json.

```sh
cbuilder run [<script>] [--arch=<arch>]
```

## Update

Updates your dependencies to the latest version according to cbuilder.json, and updates the cbuilder.lock file.

```sh
cbuilder update [<package>[:<version>]]
```