<?php

namespace App\Library;

class FileHelper
{
    /**
     * Similar to realpath() but works if file doesn't exist.
     *
     * @var string $path
     * @return string
     */
    public static function normalizePath($path)
    {
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = [];

        foreach ($parts as $part) {
            if ('.' == $part) {
                continue;
            }

            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }

        return implode(DIRECTORY_SEPARATOR, $absolutes);
    }

    /**
     * Similar to storage_path() but prevents returning path outside of storage directory.
     *
     * @var string $path
     * @return string
     */
    public static function storagePath($path)
    {
        return storage_path(static::normalizePath($path));
    }

    /**
     * Similar to mkdir() but automatically creates directories recursively and enables read/write permissions for user and group.
     *
     * @var string $path
     * @return string
     */
    public static function mkdir($path)
    {
        try {
            return mkdir($path, 0770, true);   // try creating directory, suppress error if it already exists
        } catch (\Exception $e) {
            return -1;
        }
    }
}
