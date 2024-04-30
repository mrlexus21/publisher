<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTestCase;

class AdminControllerTest extends AbstractControllerTestCase
{
    public function testGrantAuthor(): void
    {
        $user = $this->createUser('user@test.com', 'useruser');

        $username = 'admin@test.com';
        $password = 'testtest';
        $this->createAdmin($username, $password);
        $this->auth($username, $password);

        $this->client->request('POST', '/api/v1/admin/grantAuthor/'.$user->getId());

        $this->assertResponseIsSuccessful();
    }
}
