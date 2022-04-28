<?php

namespace App\Helpers;

trait MethodsHelper
{
    private static function validateMethod(string $method) : void
    {
        if (! method_exists(self::class, $method)) {
            throw new \Exception("Метод $method не существует в классе " . self::class  . "\n");
        }
    }

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
