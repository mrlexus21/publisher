<?php

namespace App\Exception;

class BookFormatNotFoundException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('book format not found');
    }
}
