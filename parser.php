<?php

// php parser.php ./acess_log
require_once __DIR__ . '/src/vendor/autoload.php';

use App\Roistat;

try {
    $parser = new Roistat\Parser('access_log');

    $parser->loadFrom('./test-sample')->fetchParseStatictics()->saveTo('JSON', 'sample.json');

    $parser->close();
} catch (\Exception $error) {
    echo $error->getMessage();
}
