# Local Repository

Directories are organized in manner in which the local repository defined in `cbuilder.json` file works:

- examples
  - org
    - package
      - cbuilder.json
      - ...
    - package-2
      - ...
    - ...
  - org-2
    - ...
  - ...
  
## Usage

In `cbuilder.json` file register repository:

```json
"repository": {
  "type": "file",
  "url": "path/to/cbuilder/examples"
}
```

And then packages can be installed like the other ones:

```sh
cbuilder require org/package
```

By default file repositories has the highest priority and `org/package` will be searched firstly in the file repositories, then in official repository registers and at the end in github.