<?php

namespace App\Exception;

class SubscriberAlreadyExistsException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('subscriber already exists');
    }
}
