<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Entity\BookToBookFormat;
use App\Exception\BookCategoryNotFoundException;
use App\Mapper\BookMapper;
use App\Model\BookCategory as BookCategoryModel;
use App\Model\BookDetails;
use App\Model\BookFormat;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use App\Repository\ReviewRepository;
use App\Service\Reccomendation\Model\RecommendationItem;
use App\Service\Reccomendation\RecommendationService;
use Doctrine\Common\Collections\Collection;
use Psr\Log\LoggerInterface;

class BookService
{
    public function __construct(
        private BookRepository $bookRepository,
        private BookCategoryRepository $bookCategoryRepository,
        private ReviewRepository $reviewRepository,
        private RatingService $ratingService,
        private RecommendationService $recommendationService,
        private LoggerInterface $logger,
    ) {
    }

    public function getBooksByCategory(int $bookCategoryId): BookListResponse
    {
        if (!$this->bookCategoryRepository->existById($bookCategoryId)) {
            throw new BookCategoryNotFoundException();
        }

        return new BookListResponse(array_map(
            fn (Book $book) => BookMapper::map($book, new BookListItem()),
            $this->bookRepository->findBooksByCategoryId($bookCategoryId)
        ));
    }

    private function getRecommendations(int $bookId): array
    {
        $ids = array_map(
            fn (RecommendationItem $item) => $item->getId(),
            $this->recommendationService->getRecommendationByBookId($bookId)->getRecommendations()
        );

        return array_map([BookMapper::class, 'mapRecommended'], $this->bookRepository->findBooksById($ids));
    }

    public function getBookById(int $id): BookDetails
    {
        $book = $this->bookRepository->getById($id);
        $reviews = $this->reviewRepository->countByBookId($id);
        $recommendations = [];

        $categories = $book->getCategories()
            ->map(fn (BookCategory $bookCategory) => new BookCategoryModel(
                $bookCategory->getId(),
                $bookCategory->getTitle(),
                $bookCategory->getSlug()
            ));
        try {
            $recommendations = $this->getRecommendations($id);
        } catch (\Exception $ex) {
            $this->logger->error(
                'error while fetching recommendations',
                [
                    'exception' => $ex->getMessage(),
                    'bookId' => $id,
                ]
            );
        }

        return BookMapper::map($book, new BookDetails())
            ->setRating($this->ratingService->calcReviewRatingForBook($id, $reviews))
            ->setReviews($reviews)
            ->setRecommendations($recommendations)
            ->setCategories($categories->toArray())
            ->setFormats($this->mapFormats($book->getFormats()));
    }

    /**
     * @param Collection<BookToBookFormat> $formats
     */
    private function mapFormats(Collection $formats): array
    {
        return $formats->map(
            fn (BookToBookFormat $formatJoin) => (new BookFormat())
                ->setTitle($formatJoin->getFormat()->getId())
                ->setDescription($formatJoin->getFormat()->getDescription())
                ->setComment($formatJoin->getFormat()->getComment())
                ->setPrice($formatJoin->getPrice())
                ->setDiscountPercent($formatJoin->getDiscountPercent())
        )->toArray();
    }
}
