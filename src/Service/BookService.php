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
use Doctrine\Common\Collections\Collection;

class BookService
{
    public function __construct(
        private BookRepository $bookRepository,
        private BookCategoryRepository $bookCategoryRepository,
        private RatingService $ratingService
    ) {
    }

    public function getBooksByCategory(int $bookCategoryId): BookListResponse
    {
        if (!$this->bookCategoryRepository->existById($bookCategoryId)) {
            throw new BookCategoryNotFoundException();
        }

        return new BookListResponse(array_map(
            fn (Book $book) => BookMapper::map($book, new BookListItem()),
            $this->bookRepository->findPublishedBooksByCategoryId($bookCategoryId)
        ));
    }

    public function getBookById(int $id): BookDetails
    {
        $book = $this->bookRepository->getPublishedById($id);

        $categories = $book->getCategories()
            ->map(fn (BookCategory $bookCategory) => new BookCategoryModel(
                $bookCategory->getId(),
                $bookCategory->getTitle(),
                $bookCategory->getSlug()
            ));
        $rating = $this->ratingService->calcReviewRatingForBook($id);

        return BookMapper::map($book, new BookDetails())
            ->setRating($rating->getRating())
            ->setReviews($rating->getTotal())
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
