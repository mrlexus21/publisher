<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Entity\BookToBookFormat;
use App\Exception\BookCategoryNotFoundException;
use App\Model\BookCategory as BookCategoryModel;
use App\Model\BookDetails;
use App\Model\BookFormat;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use App\Repository\ReviewRepository;
use Doctrine\Common\Collections\Collection;

class BookService
{
    public function __construct(
        private BookRepository $bookRepository,
        private BookCategoryRepository $bookCategoryRepository,
        private ReviewRepository $reviewRepository
    ) {
    }

    public function getBooksByCategory(int $bookCategoryId): BookListResponse
    {
        if (!$this->bookCategoryRepository->existById($bookCategoryId)) {
            throw new BookCategoryNotFoundException();
        }

        return new BookListResponse(array_map(
            [$this, 'map'],
            $this->bookRepository->findBooksByCategoryId($bookCategoryId)
        ));
    }

    public function getBookById(int $id): BookDetails
    {
        $book = $this->bookRepository->getById($id);
        $reviews = $this->reviewRepository->countByBookId($id);
        $rating = 0;
        if ($reviews > 0) {
            $rating = $this->reviewRepository->getBookTotalRatingSum($id) / $reviews;
        }
        $categories = $book->getCategories()
            ->map(fn (BookCategory $bookCategory) => new BookCategoryModel(
                $bookCategory->getId(),
                $bookCategory->getTitle(),
                $bookCategory->getSlug()
            ));

        return (new BookDetails())
            ->setId($book->getId())
            ->setTitle($book->getTitle())
            ->setSlug($book->getSlug())
            ->setImage($book->getImage())
            ->setAuthors($book->getAuthors())
            ->setMeap($book->isMeap())
            ->setPublicationData($book->getPublicationDate()->getTimestamp())
            ->setRating($rating)
            ->setReviews($reviews)
            ->setCategories($categories->toArray())
            ->setFormats($this->mapFormat($book->getFormats()));
    }

    /**
     * @param Collection<BookToBookFormat> $formats
     */
    private function mapFormat(Collection $formats): array
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

    private function map(Book $book): BookListItem
    {
        return (new BookListItem())
            ->setId($book->getId())
            ->setTitle($book->getTitle())
            ->setSlug($book->getSlug())
            ->setImage($book->getImage())
            ->setAuthors($book->getAuthors())
            ->setMeap($book->isMeap())
            ->setPublicationData($book->getPublicationDate()->getTimestamp());
    }
}
