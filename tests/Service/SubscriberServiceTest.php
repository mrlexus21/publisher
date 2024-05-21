<?php

namespace App\Tests\Service;

use App\Entity\Subscriber;
use App\Exception\SubscriberAlreadyExistsException;
use App\Model\SubscriberRequest;
use App\Repository\SubscriberRepository;
use App\Service\SubscriberService;
use App\Tests\AbstractTestCase;
use Doctrine\ORM\EntityManagerInterface;

class SubscriberServiceTest extends AbstractTestCase
{
    private SubscriberRepository $subscriberRepository;

    private EntityManagerInterface $entityManager;

    private const EMAIL = 'test@test.ru';

    protected function setUp(): void
    {
        parent::setUp();
        $this->subscriberRepository = $this->createMock(SubscriberRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testSubscribeAlreadyExists(): void
    {
        $this->expectException(SubscriberAlreadyExistsException::class);

        $this->subscriberRepository->expects($this->once())
            ->method('existByEmail')
            ->with(self::EMAIL)
            ->willReturn(true);

        $subscribeRequest = new SubscriberRequest();
        $subscribeRequest->setEmail(self::EMAIL);

        (new SubscriberService($this->subscriberRepository, $this->entityManager))->subscribe($subscribeRequest);
    }

    public function testSubscribe(): void
    {
        $this->subscriberRepository->expects($this->once())
            ->method('existByEmail')
            ->with(self::EMAIL)
            ->willReturn(false);

        $expectSubscriber = new Subscriber();
        $expectSubscriber->setEmail(self::EMAIL);

        /*$this->entityManager->expects($this->once())
            ->method('persist')
            ->with($expectSubscriber);

        $this->entityManager->expects($this->once())
            ->method('flush');*/

        $subscribeRequest = new SubscriberRequest();
        $subscribeRequest->setEmail(self::EMAIL);

        $this->subscriberRepository->expects($this->once())
            ->method('saveAndCommit')
            ->with($expectSubscriber);

        (new SubscriberService($this->subscriberRepository))->subscribe($subscribeRequest);
    }
}
