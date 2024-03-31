<?php

namespace App\Tests\Service;

use App\Service\OneService;
use App\Service\TwoService;
use App\Tests\AbstractTestCase;

class OneServiceTest extends AbstractTestCase
{
    public function testGetMessage1(): void
    {
        $message = '123';

        $twoService = $this->createMock(TwoService::class);
        $twoService->expects($this->once())
            ->method('message')
            ->with($message)
            ->willReturn('123');

        $response = (new OneService($twoService))->getMessage('123');

        $this->assertEquals($message, $response);
    }
}
