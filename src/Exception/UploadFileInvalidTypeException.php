<?php

namespace App\Exception;

class UploadFileInvalidTypeException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('uploaded file type is invalid');
    }
}
