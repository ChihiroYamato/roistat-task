<?php

namespace App\Roistat\Parser;

/** ParseLoaderInterface интерфейс загрузки файлов
 *
 */
interface ParseLoaderInterface
{
    /** loadFrom() загружает файл по указанному пути
     * @param string $fileName имя файла
     *
     * @return ParseLoaderInterface возвращает класс, реализующий ParseLoaderInterface
     */
    public function loadFrom(string $fileName) : ParseLoaderInterface;

    /** close() закрывает текущую сессию парсера
     *
     * @return void
     */
    public function close() : void;
}
