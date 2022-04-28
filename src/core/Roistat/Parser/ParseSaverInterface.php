<?php

namespace App\Roistat\Parser;

/** ParseSaverInterface интерфейс сохранения работы парсера
 *
 */
interface ParseSaverInterface
{
    /** saveTo() сохраняет результат работы парсера
     * @param string $saveType тип сохранения,
     * @param string $fileName название файла
     *
     * @return void
     */
    public function saveTo(string $saveType, string $fileName) : void;
}
