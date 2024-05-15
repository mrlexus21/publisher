<?php

namespace App\Service;

use App\Entity\Subscriber;
use App\Exception\SubscriberAlreadyExistsException;
use App\Model\SubscriberRequest;
use App\Repository\SubscriberRepository;

class SubscriberService
{
    public function __construct(private SubscriberRepository $subscriberRepository)
    {
    }

    public function subscribe(SubscriberRequest $request): void
    {
        if ($this->subscriberRepository->existByEmail($request->getEmail())) {
            throw new SubscriberAlreadyExistsException();
        }

        $this->subscriberRepository->saveAndCommit((new Subscriber())->setEmail($request->getEmail()));
    }
}
