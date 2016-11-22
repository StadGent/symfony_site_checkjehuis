<?php

namespace Digip\RenovationBundle\Calculator;

class Log
{
    /**
     * @var array
     */
    protected $lines = [];

    public function add($line)
    {
        $this->lines[] = $line;
    }

    public function getLines()
    {
        return $this->lines;
    }
}
