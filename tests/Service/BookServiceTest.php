<?php

namespace App\Tests\Service;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Exception\BookCategoryNotFoundException;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use App\Service\BookService;
use App\Tests\AbstractTestCase;
use Doctrine\Common\Collections\ArrayCollection;

class BookServiceTest extends AbstractTestCase
{
    public function testGetBooksByCategoryNotFound()
    {
        $bookRepository = $this->createMock(BookRepository::class);
        $bookCategoryRepository = $this->createMock(BookCategoryRepository::class);
        $bookCategoryRepository->expects($this->once())
            ->method('find')
            ->with(120)
            ->willThrowException(new BookCategoryNotFoundException());

        $this->expectException(BookCategoryNotFoundException::class);

        $service = new BookService($bookRepository, $bookCategoryRepository);
        $service->getBooksByCategory(120);
    }

    public function testGetBooksByCategory()
    {
        $bookRepository = $this->createMock(BookRepository::class);
        $bookCategoryRepository = $this->createMock(BookCategoryRepository::class);

        $bookRepository->expects($this->once())
            ->method('findBooksByCategoryId')
            ->with(120)
            ->willReturn([$this->createBookEntity()]);

        $bookCategoryRepository->expects($this->once())
            ->method('find')
            ->with(120)
            ->willReturn(new BookCategory());

        $service = new BookService($bookRepository, $bookCategoryRepository);
        $expected = new BookListResponse([$this->createBookItemModel()]);

        $this->assertEquals($expected, $service->getBooksByCategory(120));
    }

    public function createBookEntity(): Book
    {
        $book = (new Book())
            ->setTitle('Test Book')
            ->setPublicationDate(new \DateTime('2020-10-10'))
            ->setMeap(false)
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
