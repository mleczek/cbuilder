<?php

namespace Mleczek\CBuilder\Environment;

use Exception;
use Mleczek\CBuilder\Environment\Exceptions\InvalidPathException;
use Mleczek\CBuilder\Environment\Exceptions\UnknownException;

/**
 * Manage local filesystem.
 * Use "/" as a directory separator.
 */
class Filesystem
{
    /**
     * Alias for 'workingDir'.
     *
     * @see workingDir
     */
    public function cwd()
    {
        return $this->workingDir();
    }

    /**
     * Get working directory.
     *
     * @return string
     */
    public function workingDir()
    {
        return getcwd();
    }

    /**
     * Read content of the existing file.
     *
     * @param string $file
     * @return string
     * @throws InvalidPathException
     */
    public function readFile($file)
    {
        if (!$this->existsFile($file)) {
            throw new InvalidPathException("The '$file' file doesn't exists.");
        }

        return file_get_contents($file);
    }

    /**
     * Alias for 'isFile'.
     *
     * @see isFile
     * @param string $file
     * @return bool
     */
    public function existsFile($file)
    {
        return $this->isFile($file);
    }

    /**
     * Check whether given file exists.
     *
     * @param string $file
     * @return True if file, false if directory or non existing path.
     */
    public function isFile($file)
    {
        return is_file($file);
    }

    /**
     * Create or overwrite file with given content
     * (missing path directories will be created).
     *
     * @param string $file
     * @param string $content
     */
    public function writeFile($file, $content)
    {
        $this->touchFile($file);
        file_put_contents($file, $content);
    }

    /**
     * Make file if not exists.
     *
     * @param string $file
     * @throws InvalidPathException
     * @throws UnknownException
     */
    public function touchFile($file)
    {
        // Throw if the given path is a directory.
        if ($this->isDir($file)) {
            throw new InvalidPathException("The '$file' directory already exists.");
        }

        // Create file if not exists.
        if (!$this->existsFile($file)) {
            // Ensure that the given directory exists,
            // php 'touch' function do not create dirs.
            $this->touchDir(dirname($file));

            try {
                touch($file);
            } catch (Exception $e) {
                $scriptOwner = get_current_user();
                throw new UnknownException("Cannot create '$file' file. Please check if file name is valid and the '$scriptOwner' user have appropriate permissions.");
            }
        }
    }

    /**
     * Check whether given directory exists.
     *
     * @param string $dir
     * @return True if directory, false if file or non existing path.
     */
    public function isDir($dir)
    {
        return is_dir($dir);
    }

    /**
     * Make directory if not exists.
     *
     * @param string $dir
     * @throws InvalidPathException
     * @throws UnknownException
     */
    public function touchDir($dir)
    {
        // Throw if the given path is a file.
        if ($this->isFile($dir)) {
            throw new InvalidPathException("The '$dir' file already exists.");
        }

        // Create directory if not exists.
        if (!$this->existsDir($dir)) {
            try {
                mkdir($dir, 0777, true);
            } catch (Exception $e) {
                $scriptOwner = get_current_user();
                throw new UnknownException("Cannot create '$dir' directory. Please check if dir name is valid and the '$scriptOwner' user have appropriate permissions.");
            }
        }
    }

    /**
     * Alias for 'isDir'.
     *
     * @see isDir
     * @param string $dir
     * @return bool
     */
    public function existsDir($dir)
    {
        return $this->isDir($dir);
    }

    /**
     * Search files in directory and sub-directories
     * which name match the given pattern.
     *
     * @param string $dir
     * @param string $pattern Case-sensitive regex.
     * @return string[]
     * @throws InvalidPathException Directory not exists.
     */
    public function listFiles($dir, $pattern = '*')
    {
        if (!$this->existsDir($dir)) {
            throw new InvalidPathException("Cannot list files because the '$dir' directory not exists.");
        }

        $results = [];
        foreach (scandir($dir) as $name) {
            if ($name == '.' || $name == '..') {
                continue;
            }

            $path = $this->path($dir, $name);
            if ($this->isDir($path)) {
                // Merge results from sub-directories.
                $subResults = $this->listFiles($path, $pattern);
                $results = array_merge($results, $subResults);
            } else {
                // Add file to the results if file name match given pattern.
                if ($this->isFile($path) && preg_match("/^$pattern$/u", $name)) {
                    $results[] = $path;
                }
            }
        }

        return $results;
    }

    /**
     * Get path relative to the working directory.
     *
     * @param string[] ...$parts
     * @return string
     */
    public function path(...$parts)
    {
        $path = implode('/', $parts);
        $path = preg_replace('#[\\\/]+#', '/', $path);

        return $path;
    }

    /**
     * Remove directory or file.
     *
     * @param string $path
     */
    public function removePath($path)
    {
        if ($this->isFile($path)) {
            $this->removeFile($path);
        } else {
            $this->removeDir($path);
        }
    }

    /**
     * Remove file if exists.
     *
     * @param string $file
     * @throws InvalidPathException
     * @throws UnknownException
     */
    public function removeFile($file)
    {
        // Throw if user attempt to remove directory instead of file.
        if ($this->isDir($file)) {
            throw new InvalidPathException("Cannot remove '$file' file, directory with that name already exists.");
        }

        // Skip if file not exists (eq. removed previously).
        if (!$this->existsFile($file)) {
            return;
        }

        // Remove file.
        try {
            unlink($file);
        } catch (Exception $e) {
            $scriptOwner = get_current_user();
            throw new UnknownException("Cannot remove '$file' file. Please check if other programs don't use this file and the '$scriptOwner' user have appropriate permissions.");
        }
    }

    /**
     * Remove directory if exists.
     *
     * @param string $dir
     * @throws InvalidPathException
     * @throws UnknownException
     */
    public function removeDir($dir)
    {
        // Throw if user attempt to remove file instead of directory.
        if ($this->isFile($dir)) {
            throw new InvalidPathException("Cannot remove '$dir' directory, file with that name already exists.");
        }

        // Skip if directory not exists (eq. removed previously).
        if (!$this->existsDir($dir)) {
            return;
        }

        // Empty directory before removing them.
        foreach (scandir($dir) as $name) {
            if ($name == '.' || $name == '..') {
                continue;
            }

            $path = $this->path($dir, $name);
            if ($this->isFile($path)) {
                $this->removeFile($path);
            } else {
                $this->removeDir($path);
            }
        }

        // Remove directory.
        try {
            rmdir($dir);
        } catch (Exception $e) {
            $scriptOwner = get_current_user();
            throw new UnknownException("Cannot remove '$dir' directory. Please check if the '$scriptOwner' user have appropriate permissions.");
        }
    }
}
