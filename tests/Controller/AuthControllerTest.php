<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTestCase;

class AuthControllerTest extends AbstractControllerTestCase
{
    public function testSignUp(): void
    {
        $content = json_encode([
            'firstName' => 'Vasya',
            'lastName' => 'Testov',
            'email' => 'test@test.com',
            'password' => '1234567890123',
            'confirmPassword' => '1234567890123',
        ]);
        $this->client->request('POST', '/api/v1/auth/signUp', [], [], [], $content);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['token', 'refresh_token'],
            'properties' => [
                'token' => ['type' => 'string'],
                'refresh_token' => ['type' => 'string'],
            ],
        ]);
    }
}
