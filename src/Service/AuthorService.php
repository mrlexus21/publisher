<?php

namespace App\Service;

use App\Entity\Book;
use App\Exception\BookAlreadyExistsException;
use App\Model\Author\BookListItem;
use App\Model\Author\BookListResponse;
use App\Model\Author\CreateBookRequest;
use App\Model\IdResponse;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class AuthorService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BookRepository $bookRepository,
        private SluggerInterface $slugger,
        private Security $security
    ) {
    }

    public function createBook(CreateBookRequest $request): IdResponse
    {
        $slug = $this->slugger->slug($request->getTitle());
        if ($this->bookRepository->existBySlug($slug)) {
            throw new BookAlreadyExistsException();
        }

        $book = (new Book())
            ->setTitle($request->getTitle())
            ->setSlug($slug)
            ->setMeap(false)
            ->setUser($this->security->getUser());

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return new IdResponse($book->getId());
    }

    public function deleteBook(int $id): void
    {
        $book = $this->bookRepository->getUserBookById($id, $this->security->getUser());

        $this->entityManager->remove($book);
        $this->entityManager->flush();
    }

    public function getBooks(): BookListResponse
    {
        return new BookListResponse(
            array_map([$this, 'map'], $this->bookRepository->findUserBooks($this->security->getUser()))
        );
    }

    private function map(Book $book): BookListItem
    {
        return (new BookListItem())
            ->setId($book->getId())
            ->setTitle($book->getTitle())
            ->setSlug($book->getSlug())
            ->setImage($book->getImage());
    }
}
