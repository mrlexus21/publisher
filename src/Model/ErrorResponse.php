<?php

namespace App\Model;

use OpenApi\Attributes as OA;

class ErrorResponse
{
    public function __construct(private string $message, private mixed $details = null)
    {
    }

    #[OA\Property(type: 'object')]
    public function getDetails(): mixed
    {
        return $this->details;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
