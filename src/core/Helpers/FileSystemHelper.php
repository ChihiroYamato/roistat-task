<?php

namespace App\Helpers;

/** FileSystemHelper трейт для обработки файловой системы
 *
 */
trait FileSystemHelper
{
    /** getSrcPath() метод возвращает путь до папки src
     *
     * @return string
     */
    private static function getSrcPath() : string
    {
        return dirname(__DIR__, 2);
    }

    /** getProjectPath() метод возвращает путь до папки проекта
     *
     * @return string
     */
    private static function getProjectPath() : string
    {
        return dirname(__DIR__, 3);
    }

    /** getFreeName() метод вычисляет ближайшее свободное имя для файла
     * @param string $dirPath папка сканирования
     * @param string $name базовое имя файла
     *
     * @return string
     */
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

    /** initDirectory() метод инициирует директорию
     * @param string $path путь до указанной директории
     * @return void
     * @throw \Exception
     */
    private static function initDirectory(string $path) : void
    {
        if (! is_dir($path) && ! mkdir($path, 0777, true)) {
            throw new \Exception("Не удалось создать директории по пути $path\n");
        }
    }

    /** removeDirectory() метод удаляет укаанную папку
     * @param string $path путь до указанной директории
     * @return void
     * @throw \Exception
     */
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
