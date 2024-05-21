<?php

namespace App\Tests\Service;

use App\Entity\Book;
use App\Entity\Review;
use App\Model\ReviewPage;
use App\Repository\ReviewRepository;
use App\Service\Rating;
use App\Service\RatingService;
use App\Service\ReviewService;
use App\Tests\AbstractTestCase;
use Doctrine\Common\Collections\ArrayCollection;

class ReviewServiceTest extends AbstractTestCase
{
    private ReviewRepository $reviewRepository;
    private RatingService $ratingService;

    private const BOOK_ID = 1;
    private const PER_PAGE = 5;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reviewRepository = $this->createMock(ReviewRepository::class);
        $this->ratingService = $this->createMock(RatingService::class);
    }

    public static function dataProvider(): array
    {
        return [
            [0, 0],
            [-1, 0],
            [-20, 0],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetReviewPageByBookIdInvalidPage(int $page, int $offset): void
    {
        $this->ratingService->expects($this->once())
            ->method('calcReviewRatingForBook')
            ->with(self::BOOK_ID)
            ->willReturn(new Rating(0, 0.0));

        $this->reviewRepository->expects($this->once())
            ->method('getPageByBookId')
            ->with(self::BOOK_ID, $offset, self::PER_PAGE)
            ->willReturn(new \ArrayIterator());

        $service = new ReviewService($this->reviewRepository, $this->ratingService);
        $expected = (new ReviewPage())
            ->setTotal(0)
            ->setRating(0)
            ->setPage($page)
            ->setPages(0)
            ->setPerPage(self::PER_PAGE)
            ->setItems([]);

        $this->assertEquals($expected, $service->getReviewPageByBookId(self::BOOK_ID, $page));
    }

    public function testGetReviewPageByBookId(): void
    {
        $this->ratingService->expects($this->once())
            ->method('calcReviewRatingForBook')
            ->with(self::BOOK_ID)
            ->willReturn(new Rating(1, 4.0));

        $entity = (new Review())
            ->setRating(4)
            ->setCreatedAt(new \DateTimeImmutable('2024-01-02'))
            ->setAuthor('test')
            ->setContent('test');
        $this->setEntityId($entity, 1);

        $this->reviewRepository->expects($this->once())
            ->method('getPageByBookId')
            ->with(self::BOOK_ID, 0, self::PER_PAGE)
            ->willReturn(new \ArrayIterator([
                $entity,
            ]));

        $service = new ReviewService($this->reviewRepository, $this->ratingService);
        $expected = (new ReviewPage())
            ->setTotal(1)
            ->setRating(4)
            ->setPage(1)
            ->setPages(1)
            ->setPerPage(self::PER_PAGE)
            ->setItems([
                (new \App\Model\Review())
                    ->setId($entity->getId())
                    ->setRating($entity->getRating())
                    ->setCreatedAt($entity->getCreatedAt()?->getTimestamp())
                    ->setAuthor($entity->getAuthor())
                    ->setContent($entity->getContent()),
            ]);

        $this->assertEquals($expected, $service->getReviewPageByBookId(self::BOOK_ID, 1));
    }

    public function createBookEntity(): Book
    {
        $book = (new Book())
            ->setTitle('Test Book')
            ->setPublicationDate(new \DateTimeImmutable('2020-10-10'))
            ->setIsbn('123456')
            ->setDescription('test description')
            ->setAuthors(['Tester'])
            ->setSlug('test-book')
            ->setImage('image')
            ->setCategories(new ArrayCollection([]));
        $this->setEntityId($book, 123);

        return $book;
    }
}
