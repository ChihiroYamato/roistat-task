<?php

namespace App\Roistat\Parser;

use App\Helpers;

/** ParseSaver класс, реализующий сохранение работы парсера
 *
 */
class ParseSaver implements ParseSaverInterface
{
    use Helpers\MethodsHelper, Helpers\FileSystemHelper;

    private const OUTPUT_DIRECTORY = 'output';

    private array $buffer;

    public function __construct(array $buffer)
    {
        $this->buffer = $buffer;
    }

    public function saveTo(string $saveType, string $fileName) : void
    {
        $saveMethod = self::prepareMethodName($saveType, 'save');
        self::validateMethod($saveMethod);

        $this->{$saveMethod}($fileName);
    }

    /** saveJson() реализация типа сохранения Json
     * @param string $fileName
     *
     * @return void
     * @throw \Exception
     */
    private function saveJson(string $fileName) : void
    {
        $outputPath = self::getProjectPath() . '/' . self::OUTPUT_DIRECTORY;
        $currentFileName = self::getFreeName($outputPath, $fileName);
        $size = file_put_contents($currentFileName, json_encode($this->buffer, JSON_FORCE_OBJECT));

        if ($size === false) {
            throw new \Exception("Не удалось сохранить файл $currentFileName\n");
        }
    }
}
