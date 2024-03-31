<?php

namespace App\Service;

class OneService
{
    private TwoService $twoService;

    public function __construct(TwoService $twoService)
    {
        $this->twoService = $twoService;
    }

    public function getMessage($message): string
    {
        return $this->twoService->message($message);
    }
}
