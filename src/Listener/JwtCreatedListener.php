<?php

namespace App\Listener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JwtCreatedListener
{
    public function __invoke(JWTCreatedEvent $event)
    {
        $user = $event->getUser();
        $payload = $event->getData();
        $payload['id'] = $user->getUserIdentifier();

        $event->setData($payload);
    }
}
