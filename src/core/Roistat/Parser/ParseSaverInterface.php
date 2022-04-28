<?php

namespace App\Roistat\Parser;

interface ParseSaverInterface
{
    public function saveTo(string $saveType, string $fileName) : void;
}
