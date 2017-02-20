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
  "type": "local",
  "url": "path/to/cbuilder/examples"
}
```

Custom repositories have the higher priority than the public registers. The `org/package` will be searched firstly in the local repositories and then in official repository registers.
