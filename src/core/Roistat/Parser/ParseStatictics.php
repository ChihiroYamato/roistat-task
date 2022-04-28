<?php

namespace App\Roistat\Parser;

use App\Helpers;

/** ParseStatictics Класс реализует формирование статистики из загруженного файла
 *
 */
class ParseStatictics implements ParseSaverInterface
{
    use Helpers\MethodsHelper;

    private string $parseMethod;
    private array $statictics;
    private array $storage;

    /**
     * @param string $parseMethod записывает метод парсинга
     */
    public function __construct(string $parseMethod)
    {
        $this->parseMethod = self::prepareMethodName($parseMethod, 'calculate');
        self::validateMethod($this->parseMethod);

        $this->statictics = [];
        $this->storage = [];
    }

    public function saveTo(string $saveType, string $fileName) : void
    {
        $parseSaver = new ParseSaver($this->statictics);

        $parseSaver->saveTo($saveType, $fileName);
    }

    /** calculate() формирует статистику из переданного массива
     * @param array $bufferStorage массив с распаршенной информацией
     *
     * @return void
     * @throw \Exception
     */
    public function calculate(array $bufferStorage) : void
    {
        foreach ($bufferStorage as $bufferPath) {
            $buffer = unserialize(file_get_contents($bufferPath));

            if ($buffer === false) {
                throw new \Exception("Не удалось прочитать файл $bufferPath\n");
            }

            $this->{$this->parseMethod}($buffer);
        }
    }

    /** calculateParseAccessLog() реализация сбора статистики из acсess_log
     * @param array $buffer массив с распаршенной информацией
     *
     * @return void
     */
    private function calculateParseAccessLog(array $buffer) : void
    {
        foreach ($buffer as $bufferItem) {
            if (array_key_exists('views', $this->statictics)) {
                $this->statictics['views']++;
            } else {
                $this->statictics['views'] = 1;
            }

            if (array_key_exists('traffic', $this->statictics)) {
                $this->statictics['traffic'] += (int) $bufferItem['size'];
            } else {
                $this->statictics['traffic'] = (int) $bufferItem['size'];
            }

            $crawlers = [];
            preg_match('/(?<bot>\w+)(?:bot)/', $bufferItem['agent'], $crawlers);

            if (! empty($crawlers['bot'])) {
                if (
                    array_key_exists('crawlers', $this->statictics)
                    && array_key_exists($crawlers['bot'], $this->statictics['crawlers'])
                ) {
                    $this->statictics['crawlers'][$crawlers['bot']]++;
                } else {
                    $this->statictics['crawlers'][$crawlers['bot']] = 1;
                }
            }

            if (
                array_key_exists('statusCodes', $this->statictics)
                && array_key_exists($bufferItem['code'], $this->statictics['statusCodes'])
            ) {
                $this->statictics['statusCodes'][$bufferItem['code']]++;
            } else {
                $this->statictics['statusCodes'][$bufferItem['code']] = 1;
            }

            $this->storage['urls'][$bufferItem['referer']] = 1;
        }

        $this->statictics['urls'] = count($this->storage['urls']);
    }
}
