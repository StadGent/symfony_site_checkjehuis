<?php

namespace App\Calculator;

class Log
{
    /**
     * @var array
     */
    protected $lines = [];

    /**
     * Add a log line.
     *
     * @param string $line
     */
    public function add($line)
    {
        $this->lines[] = $line;
    }

    /**
     * Get the log lines.
     *
     * @return array
     */
    public function getLines()
    {
        return $this->lines;
    }
}
