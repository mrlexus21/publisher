<?php

namespace App\Service;

use App\Entity\Book;
use App\Exception\BookCategoryNotFoundException;
use App\Mapper\BookMapper;
use App\Model\BookDetails;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;

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
        $rating = $this->ratingService->calcReviewRatingForBook($id);

        return BookMapper::map($book, new BookDetails())
            ->setRating($rating->getRating())
            ->setReviews($rating->getTotal())
            ->setCategories(BookMapper::mapCategories($book))
            ->setFormats(BookMapper::mapFormats($book));
    }
}
