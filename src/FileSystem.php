<?php
declare(strict_types=1);

namespace Azurre;

class FileSystem
{
    public static function createDirectory(string $path, int $permission = 0755): bool
    {
        if (is_dir($path)) {
            return true;
        }

        return mkdir($path, $permission, true);
    }

    public static function copy(string $source, string $destination, bool $followSymlink = false): bool
    {
        if (!static::createDirectory($destination)) {
            return false;
        }

        if (is_file($source)) {
            return copy($followSymlink ? realpath($source) : $source, $destination);
        }

        $files = scandir($source);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $srcFile = $source . DIRECTORY_SEPARATOR . $file;
            $destFile = $destination . DIRECTORY_SEPARATOR . $file;
            static::copy($srcFile, $destFile, $followSymlink);
        }

        return true;
    }

    public static function move(string $source, string $destination, int $permission = 0755): bool {
        static::createDirectory($destination, $permission);
        if (is_file($source)) {
            return rename($source, $destination);
        }
        if (!is_dir($source)) {
            return false;
        }
        $files = scandir($source);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $srcFile = $source . DIRECTORY_SEPARATOR . $file;
            $destFile = $destination . DIRECTORY_SEPARATOR . $file;
            static::move($srcFile, $destFile);
        }

        return rmdir($source);
    }

    public static function remove($path): bool
    {
        if (is_file($path)) {
            return unlink($path);
        }
        if (!is_dir($path)) {
            return false;
        }
        $files = scandir($path);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $filePath = $path . DIRECTORY_SEPARATOR . $file;
            static::remove($filePath);
        }

        return rmdir($path);
    }

    public static function chmod(string $path, int $permission, bool $recursive = false): bool {
        if (is_file($path)) {
            echo "Chmod $path with $permission\n";
            return chmod($path, $permission);
        }
        if (!is_dir($path)) {
            return false;
        }

        $files = scandir($path);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $filePath = $path . DIRECTORY_SEPARATOR . $file;
            echo "$filePath\n";
            static::chmod($filePath, $permission, $recursive);
        }

        return true;
    }
}
