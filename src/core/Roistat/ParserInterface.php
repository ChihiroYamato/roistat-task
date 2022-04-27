<?php

namespace App\Roistat;

interface ParserInterface
{
    public function loadFrom(string $filePath) : ParserInterface;

    public function saveTo(string $filePath) : ParserInterface;
}
