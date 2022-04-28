<?php

namespace App\Helpers;

trait FileSystemHelper
{
    private static function getSrcPath() : string
    {
        return dirname(__DIR__, 2);
    }

    private static function getProjectPath() : string
    {
        return dirname(__DIR__, 3);
    }

    private static function getFreeName(string $dirPath, string $name) : string
    {
        self::initDirectory($dirPath);

        $filePath = "$dirPath/$name";

        if (! file_exists($filePath)) {
            return $filePath;
        }

        $matches = [];
        preg_match('/(?<name>^[^\.]+)(?<ext>\..+$)?/', $filePath, $matches);
        $filePath = $matches['name'];
        $extension = $matches['ext'] ?? '';
        $count = 1;

        while (file_exists("{$filePath}_{$count}{$extension}")) {
            $count++;
        }

        return "{$filePath}_{$count}{$extension}";
    }

    private static function initDirectory(string $path) : void
    {
        if (! is_dir($path) && ! mkdir($path, 0777, true)) {
            throw new \Exception("Не удалось создать директории по пути $path\n");
        }
    }

    private static function removeDirectory(string $path) : void
    {
        foreach (scandir($path) as $file) {
            if (! in_array($file, ['.', '..']) && ! unlink("$path/$file")) {
                throw new \Exception("Не удалось удалить файл по пути $path/$file\n");
            }
        }

        if (! rmdir($path)) {
            throw new \Exception("Не удалось удалить директории по пути $path\n");
        }
    }
}
