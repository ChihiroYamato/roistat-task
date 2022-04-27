<?php

namespace App\Roistat;

class Parser implements ParserInterface
{
    public function __construct()
    {

    }

    public function loadFrom(string $filePath) : Parser
    {
        return $this;
    }

    public function saveTo(string $filePath) : Parser
    {
        return $this;
    }
}
