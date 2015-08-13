<?php
namespace Models;

class Log {
    private $file;
    public function __construct($file)
    {
        $this->file = $file;
    }

    public function log($string)
    {
        file_put_contents($this->file, $string);
    }
}