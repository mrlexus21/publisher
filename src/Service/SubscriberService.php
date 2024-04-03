<?php

namespace App\Service;

use App\Entity\Subscriber;
use App\Exception\SubscriberAlreadyExistsException;
use App\Model\SubscriberRequest;
use App\Repository\SubscriberRepository;
use Doctrine\ORM\EntityManagerInterface;

class SubscriberService
{
    public function __construct(private SubscriberRepository $subscriberRepository, private EntityManagerInterface $entityManager)
    {
    }

    public function subscribe(SubscriberRequest $request): void
    {
        if ($this->subscriberRepository->existByEmail($request->getEmail())) {
            throw new SubscriberAlreadyExistsException();
        }

        $subscribe = new Subscriber();
        $subscribe->setEmail($request->getEmail());

        $this->entityManager->persist($subscribe);
        $this->entityManager->flush();
    }
}
