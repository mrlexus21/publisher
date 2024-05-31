<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\RoleService;
use App\Tests\AbstractTestCase;

class RoleServiceTest extends AbstractTestCase
{
    private UserRepository $userRepository;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = new User();

        $this->userRepository = $this->createMock(UserRepository::class);
        $this->userRepository->expects($this->once())
            ->method('getUser')
            ->with(1)
            ->willReturn($this->user);

        $this->userRepository->expects($this->once())
            ->method('commit');
    }

    public function createService(): RoleService
    {
        return new RoleService($this->userRepository);
    }

    public function testGrantAuthor(): void
    {
        $this->createService()->grantAuthor(1);
        $this->assertEquals(['ROLE_AUTHOR'], $this->user->getRoles());
    }

    public function testGrantAdmin(): void
    {
        $this->createService()->grantAdmin(1);
        $this->assertEquals(['ROLE_ADMIN'], $this->user->getRoles());
    }
}
