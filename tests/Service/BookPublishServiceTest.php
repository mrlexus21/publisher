<?php

namespace App\Tests\Service;

use App\Entity\Book;
use App\Model\Author\PublishBookRequest;
use App\Repository\BookRepository;
use App\Service\BookPublishService;
use App\Tests\AbstractTestCase;

class BookPublishServiceTest extends AbstractTestCase
{
    private BookRepository $bookRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookRepository = $this->createMock(BookRepository::class);
    }

    public function testPublish(): void
    {
        $book = new Book();
        $datetime = new \DateTimeImmutable('2020-10-11');
        $request = new PublishBookRequest();
        $request->setDate($datetime);

        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        $this->bookRepository->expects($this->once())
            ->method('commit');

        (new BookPublishService($this->bookRepository))->publish(1, $request);

        $this->assertEquals($book->getPublicationDate(), $datetime);
    }

    public function testUnpublish(): void
    {
        $book = new Book();
        $book->setPublicationDate(new \DateTimeImmutable('2020-10-11'));

        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        $this->bookRepository->expects($this->once())
            ->method('commit');

        (new BookPublishService($this->bookRepository))->unpublish(1);

        $this->assertNull($book->getPublicationDate());
    }
}
