<?php

namespace App\Tests\Service;

use App\Entity\Book;
use App\Exception\BookCategoryNotFoundException;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use App\Repository\ReviewRepository;
use App\Service\BookService;
use App\Tests\AbstractTestCase;
use Doctrine\Common\Collections\ArrayCollection;

class BookServiceTest extends AbstractTestCase
{
    public function testGetBooksByCategoryNotFound()
    {
        $reviewRepository = $this->createMock(ReviewRepository::class);
        $bookRepository = $this->createMock(BookRepository::class);
        $bookCategoryRepository = $this->createMock(BookCategoryRepository::class);
        $bookCategoryRepository->expects($this->once())
            ->method('existById')
            ->with(120)
            ->willReturn(false);

        $this->expectException(BookCategoryNotFoundException::class);

        $service = new BookService($bookRepository, $bookCategoryRepository, $reviewRepository);
        $service->getBooksByCategory(120);
    }

    public function testGetBooksByCategory()
    {
        $reviewRepository = $this->createMock(ReviewRepository::class);
        $bookRepository = $this->createMock(BookRepository::class);
        $bookCategoryRepository = $this->createMock(BookCategoryRepository::class);

        $bookRepository->expects($this->once())
            ->method('findBooksByCategoryId')
            ->with(120)
            ->willReturn([$this->createBookEntity()]);

        $bookCategoryRepository->expects($this->once())
            ->method('existById')
            ->with(120)
            ->willReturn(true);

        $service = new BookService($bookRepository, $bookCategoryRepository, $reviewRepository);
        $expected = new BookListResponse([$this->createBookItemModel()]);

        $this->assertEquals($expected, $service->getBooksByCategory(120));
    }

    public function createBookEntity(): Book
    {
        $book = (new Book())
            ->setTitle('Test Book')
            ->setPublicationDate(new \DateTimeImmutable('2020-10-10'))
            ->setMeap(false)
            ->setIsbn('123456')
            ->setDescription('test description')
            ->setAuthors(['Tester'])
            ->setSlug('test-book')
            ->setImage('image')
            ->setCategories(new ArrayCollection([]));
        $this->setEntityId($book, 123);

        return $book;
    }

    public function createBookItemModel(): BookListItem
    {
        return (new BookListItem())
            ->setId(123)
            ->setTitle('Test Book')
            ->setSlug('test-book')
            ->setAuthors(['Tester'])
            ->setMeap(false)
            ->setImage('image')
            ->setPublicationData(1602288000);
    }
}
