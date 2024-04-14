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
use App\Service\RatingService;
use App\Tests\AbstractTestCase;
use Doctrine\Common\Collections\ArrayCollection;

class BookServiceTest extends AbstractTestCase
{
    private ReviewRepository $reviewRepository;
    private BookRepository $bookRepository;
    private BookCategoryRepository $bookCategoryRepository;
    private RatingService $ratingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reviewRepository = $this->createMock(ReviewRepository::class);
        $this->bookRepository = $this->createMock(BookRepository::class);
        $this->bookCategoryRepository = $this->createMock(BookCategoryRepository::class);
        $this->ratingService = $this->createMock(RatingService::class);
    }

    public function testGetBooksByCategoryNotFound()
    {
        $this->bookCategoryRepository->expects($this->once())
            ->method('existById')
            ->with(120)
            ->willReturn(false);

        $this->expectException(BookCategoryNotFoundException::class);

        $this->createBookService()->getBooksByCategory(120);
    }

    public function testGetBooksByCategory()
    {
        $this->bookRepository->expects($this->once())
            ->method('findBooksByCategoryId')
            ->with(120)
            ->willReturn([$this->createBookEntity()]);

        $this->bookCategoryRepository->expects($this->once())
            ->method('existById')
            ->with(120)
            ->willReturn(true);

        $expected = new BookListResponse([$this->createBookItemModel()]);

        $this->assertEquals($expected, $this->createBookService()->getBooksByCategory(120));
    }

    private function createBookService(): BookService
    {
        return new BookService(
            $this->bookRepository,
            $this->bookCategoryRepository,
            $this->reviewRepository,
            $this->ratingService
        );
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
            ->setPublicationDate(1602288000);
    }
}
