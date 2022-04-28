<?php

namespace App\Roistat;

use App\Helpers;

/** Parser Основной класс парсинга в задании
 *
 */
class Parser implements Parser\ParseLoaderInterface, Parser\ParseSaverInterface
{
    use Helpers\MethodsHelper, Helpers\FileSystemHelper;

    private const MAX_BUFFER_COUNT = 65536;
    private const BUFFER_DIRECTORY = 'buffer';

    private string $parseMethod;
    private array $bufferStorage;
    private array $buffer;

    /**
     * @param string $parseType сохраняет тип парсинга
     */
    public function __construct(string $parseType)
    {
        $parseMethod = self::prepareMethodName($parseType, 'parse');
        self::validateMethod($parseMethod);

        $this->parseMethod = $parseMethod;
        $this->bufferStorage = [];
        $this->buffer = [];
    }

    public function loadFrom(string $fileName) : Parser
    {
        if (! file_exists($fileName)) {
            throw new \Exception("Файл $fileName не существует\n");
        }

        $notesCount = 0;
        $file = fopen($fileName, 'r');

        while (true) {
            $line = fgets($file);

            if ($line === false) {
                break;
            }

            $this->buffer[$notesCount] = $this->{$this->parseMethod}($line);

            if (! is_array($this->buffer[$notesCount])) {
                throw new \Exception($this->parseMethod . " должен возвращать массив\n");
            }

            $notesCount++;

            if ($notesCount >= self::MAX_BUFFER_COUNT) {
                $this->storeBuffer();
                $notesCount = 0;
            }
        }

        if (count($this->buffer) > 0) {
            $this->storeBuffer();
        }

        fclose($file);

        return $this;
    }


    public function saveTo(string $saveType, string $fileName) : void
    {
        foreach ($this->bufferStorage as $bufferPath) {
            $buffer = unserialize(file_get_contents($bufferPath));

            if ($buffer === false) {
                throw new \Exception("Не удалось прочитать файл $bufferPath\n");
            }

            $parseSaver = new Parser\ParseSaver($buffer);

            $parseSaver->saveTo($saveType, $fileName);

            unset($parseSaver);
        }
    }

    public function close() : void
    {
        self::removeDirectory(self::getSrcPath() . '/' . self::BUFFER_DIRECTORY);
    }

    /** fetchParseStatictics() возвращает экземпляр объекта ParseStatictics со сформированной
     * и готовой к сохранению статистикой
     *
     * @return Parser\ParseStatictics
     */
    public function fetchParseStatictics() : Parser\ParseStatictics
    {
        $parseStatictics = new Parser\ParseStatictics($this->parseMethod);
        $parseStatictics->calculate($this->bufferStorage);

        return $parseStatictics;
    }

    /** storeBuffer() временно сохраняет массивы буфера в корне проекта для оптимизации чтения
     *
     * @return void
     * throw \Exception
     */
    private function storeBuffer() : void
    {
        $bufferPath = self::getSrcPath() . '/' . self::BUFFER_DIRECTORY;
        $fileName = self::getFreeName($bufferPath, 'buffer_storage');
        $size = file_put_contents($fileName, serialize($this->buffer));

        if ($size === false) {
            throw new \Exception("Не удалось сохранить файл $fileName\n");
        }

        $this->bufferStorage[] = $fileName;
        $this->buffer = [];
    }

    /** parseAccessLog() Реализация парсинга из файлов типа access_log
     * @param string $buffer Строка из буфера
     *
     * @return array
     */
    private function parseAccessLog(string $buffer) : array
    {
        $accessLogPattern = [
            'ip' => '(?<ip>(?:\d{1,3}\.){3}\d{1,3})?',
            'client' => '(?<client>[\w\-_]+)?',
            'user' => '(?<user>[\w\-_]+)?',
            'date' => '\[(?<date>[^\]]+)\]?',
            'request' => '"(?<request>[^"]+)"?',
            'code' => '(?<code>\d{3})?',
            'size' => '(?<size>\d+)?',
            'referer' => '"(?<referer>[^"]+)"?',
            'agent' => '"(?<agent>[^"]+)"?',
        ];
        $pattern = '/' . implode(' +' , $accessLogPattern) . '/';
        $matches = [];

        preg_match($pattern, $buffer, $matches);

        return [
            'ip' => $matches['ip'],
            'client' => $matches['client'],
            'user' => $matches['user'],
            'date' => $matches['date'],
            'request' => $matches['request'],
            'code' => $matches['code'],
            'size' => $matches['size'],
            'referer' => $matches['referer'],
            'agent' => $matches['agent'],
        ];
    }
}
