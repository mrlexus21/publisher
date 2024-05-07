<?php

namespace App\Service;

use App\Entity\Book;
use App\Exception\BookAlreadyExistsException;
use App\Model\Author\BookListItem;
use App\Model\Author\BookListResponse;
use App\Model\Author\CreateBookRequest;
use App\Model\Author\UploadCoverResponse;
use App\Model\IdResponse;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AuthorBookService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BookRepository $bookRepository,
        private SluggerInterface $slugger,
        private UploadService $uploadService
    ) {
    }

    public function uploadCover(int $id, UploadedFile $file): UploadCoverResponse
    {
        $book = $this->bookRepository->getBookById($id);
        $oldImage = $book->getImage();

        $link = $this->uploadService->uploadBookFile($id, $file);
        $book->setImage($link);

        $this->entityManager->flush();

        if (null !== $oldImage) {
            $this->uploadService->deleteBookFile($book->getId(), basename($oldImage));
        }

        return new UploadCoverResponse($link);
    }

    public function createBook(CreateBookRequest $request, UserInterface $user): IdResponse
    {
        $slug = $this->slugger->slug($request->getTitle());
        if ($this->bookRepository->existBySlug($slug)) {
            throw new BookAlreadyExistsException();
        }

        $book = (new Book())
            ->setTitle($request->getTitle())
            ->setSlug($slug)
            ->setMeap(false)
            ->setUser($user);

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return new IdResponse($book->getId());
    }

    public function deleteBook(int $id): void
    {
        $book = $this->bookRepository->getBookById($id);

        $this->entityManager->remove($book);
        $this->entityManager->flush();
    }

    public function getBooks(UserInterface $user): BookListResponse
    {
        return new BookListResponse(
            array_map([$this, 'map'], $this->bookRepository->findUserBooks($user))
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
