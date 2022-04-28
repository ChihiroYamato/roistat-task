<?php

if (count($argv) < 2) {
    throw new \Exception('Недостаточно аргументов для запуска скрипта');
}

require_once __DIR__ . '/src/vendor/autoload.php';

use App\Roistat;

try {
    $parser = new Roistat\Parser('access_log');

    $parser->loadFrom($argv[1])->fetchParseStatictics()->saveTo('JSON', 'sample.json');

    $parser->close();
} catch (\Exception $error) {
    echo $error->getMessage();
}
