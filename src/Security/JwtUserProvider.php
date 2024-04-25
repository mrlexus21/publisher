<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\PayloadAwareUserProviderInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method UserInterface loadUserByIdentifierAndPayload(string $identifier, array $payload)
 */
class JwtUserProvider implements PayloadAwareUserProviderInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function loadUserByIdentifierAndPayload(string $identifier, array $payload): UserInterface
    {
        return $this->getUser('id', $payload['id']);
    }

    public function loadUserByUsernameAndPayload(string $username, array $payload)
    {
        return null;
    }

    public function refreshUser(UserInterface $user)
    {
        return null;
    }

    public function supportsClass(string $class)
    {
        return User::class == $class || is_subclass_of($class, User::class);
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->getUser('email', $identifier);
    }

    private function getUser($key, $value): UserInterface
    {
        $user = $this->userRepository->findOneBy([$key => $value]);
        if (null == $user) {
            $e = new UserNotFoundException('User with id'.json_encode($value).'not found');
            $e->setUserIdentifier(json_encode($value));

            throw $e;
        }

        return $user;
    }
}
