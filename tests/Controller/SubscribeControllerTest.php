<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTestCase;
use Symfony\Component\HttpFoundation\Response;

class SubscribeControllerTest extends AbstractControllerTestCase
{
    public function testSubscribe(): void
    {
        $content = json_encode(['email' => 'test@test.ru', 'agreed' => true]);
        $this->client->request('POST', '/api/v1/subscribe', [], [], [], $content);

        $this->assertResponseIsSuccessful();
    }

    public function testSubscribeNotAgreed(): void
    {
        $content = json_encode(['email' => 'test@test.ru']);
        $this->client->request('POST', '/api/v1/subscribe', [], [], [], $content);

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->client->request('POST', '/api/v1/subscribe', [], [], [], $content);
        $this->assertJsonDocumentMatches($responseContent, [
            '$.message' => 'validation failed',
            '$.details.violations' => self::countOf(1),
            '$.details.violations[0].field' => 'agreed',
        ]);
    }
}
