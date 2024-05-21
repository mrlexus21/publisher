<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Exception\UserAlreadyExistsException;
use App\Model\SignUpRequest;
use App\Repository\UserRepository;
use App\Service\SignUpService;
use App\Tests\AbstractTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;

class SignUpServiceTest extends AbstractTestCase
{
    private UserPasswordHasher $hasher;
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;
    private AuthenticationSuccessHandler $successHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hasher = $this->createMock(UserPasswordHasher::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->successHandler = $this->createMock(AuthenticationSuccessHandler::class);
    }

    private function createService(): SignUpService
    {
        return new SignUpService($this->hasher, $this->userRepository, $this->successHandler);
    }

    public function testSignUpUserAlreadyExists()
    {
        $this->expectException(UserAlreadyExistsException::class);
        $this->userRepository->expects($this->once())
            ->method('existsByEmail')
            ->with('test@test.com')
            ->willReturn(true);

        $this->createService()->signUp((new SignUpRequest())->setEmail('test@test.com'));
    }

    public function testSignUp()
    {
        $this->userRepository->expects($this->once())
            ->method('existsByEmail')
            ->with('test@test.com')
            ->willReturn(false);

        $expectedHasherUser = (new User())
            ->setRoles(['ROLE_USER'])
            ->setFirstName('Vasya')
            ->setLastName('Testov')
            ->setEmail('test@test.com');

        $this->hasher->expects($this->once())
            ->method('hashPassword')
            ->with($expectedHasherUser, 'testtest')
            ->willReturn('hashed_password');

        $expectedUser = clone $expectedHasherUser;
        $expectedUser->setPassword('hashed_password');

        $this->userRepository->expects($this->once())
            ->method('saveAndCommit')
            ->with($expectedUser);

        $response = new Response();
        $this->successHandler->expects($this->once())
            ->method('handleAuthenticationSuccess')
            ->with($expectedUser)
            ->willReturn($response);

        $signUpRequest = (new SignUpRequest())
            ->setFirstName('Vasya')
            ->setLastName('Testov')
            ->setEmail('test@test.com')
            ->setPassword('testtest');

        $this->assertEquals(
            $response,
            $this->createService()->signUp($signUpRequest)
        );
    }
}
