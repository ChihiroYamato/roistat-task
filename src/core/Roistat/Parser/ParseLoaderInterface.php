<?php

namespace App\Roistat\Parser;

interface ParseLoaderInterface
{
    public function loadFrom(string $fileName) : ParseLoaderInterface;

    public function close() : void;
}
