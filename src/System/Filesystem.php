<?php

namespace Mleczek\CBuilder\System;

/**
 * Helper class to operate on the local file system.
 */
class Filesystem
{
    /**
     * Get files in directory that match given pattern.
     *
     * @param string $dir
     * @param string $pattern File name pattern (regex).
     * @param bool $nested Search in subdirectories.
     * @return string[] List of files relative to the dir.
     */
    public function walk($dir, $pattern = '.*', $nested = true)
    {
        // Check if provided valid dir path
        if (! is_dir($dir)) {
            return [];
        }

        $results = [];
        $files = scandir($dir, SCANDIR_SORT_NONE);
        foreach ($files as $f) {
            $path = "$dir/$f";

            // Skip current and parent dir pointers
            if ($f == '.' || $f == '..') {
                continue;
            }

            // Search subdirectories if enabled
            if (is_dir($path) && $nested) {
                $subResults = $this->walk($path, $pattern, $nested);
                $results = array_merge($results, $subResults);
            }

            // Add file to results if match pattern
            if (is_file($path) && preg_match($pattern, $f) == 1) {
                $results[] = $path;
            }
        }

        return $results;
    }

    /**
     * Remove file or directory (works with non-empty dirs).
     *
     * Directories symbolic links are removed without
     * removing any of the original files and directories.
     *
     * @param string $path
     * @return bool True on success, false otherwise.
     */
    public function remove($path)
    {
        // Remove file and stop
        if (! is_dir($path)) {
            return unlink($path);
        }

        // To remove directory it must be empty
        if(!is_link($path)) {
            foreach (scandir($path) as $item) {
                if ($item == '.' || $item == '..') {
                    continue;
                }

                $this->remove($path . '/' . $item);
            }
        }

        return rmdir($path);
    }

    /**
     * Create directory if not exists.
     *
     * @param string $path
     * @param int $mode
     * @return bool True on success, false otherwise.
     */
    public function makeDir($path, $mode = 0777)
    {
        // Skip if directory exists
        if (is_dir($path)) {
            return true;
        }

        // Error, cannot create dir
        // (file exists with given name)
        if (is_file($path)) {
            return false;
        }

        return mkdir($path, $mode, true);
    }
}
