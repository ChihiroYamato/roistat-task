<?php

namespace App\Helpers;

/** MethodsHelper Трейт по обработке методов
 *
 */
trait MethodsHelper
{
    /** validateMethod() проверяет наличие указанного метода в классе, использующем трейт
     * @param string $method название метода
     *
     * @return void
     * @throw \Exception
     */
    private static function validateMethod(string $method) : void
    {
        if (! method_exists(self::class, $method)) {
            throw new \Exception("Метод $method не существует в классе " . self::class  . "\n");
        }
    }

    /** prepareMethodName() форматирует переданное название в корректное camelcase название метода
     * @param string $name имя метода
     * @param string $prefix [optional] префикс к имени метода
     *
     * @return string
     */
    private static function prepareMethodName(string $name, string $prefix = '') : string
    {
        $PartsName = explode('_', preg_replace('/[^\w]/', '_', strtolower($name)));
        $PartsName = array_filter($PartsName, fn ($part) => strlen($part) > 0);
        $PartsName = array_map(fn ($part) => strtoupper($part[0]) . substr($part, 1), $PartsName);

        $methodName = implode('', $PartsName);

        $preparedPrefix = preg_replace('/[^\w]/', '', $prefix);
        if (strlen($preparedPrefix) > 0) {
            $methodName = strtolower($preparedPrefix) . $methodName;
        } else {
            $methodName = strtolower($methodName[0]) . substr($methodName, 1);
        }

        return $methodName;
    }
}
