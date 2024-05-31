<?php

declare(strict_types=1);

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Attribute\RequestFile;
use App\Model\Author\BookDetails;
use App\Model\Author\BookListResponse;
use App\Model\Author\CreateBookChapterRequest;
use App\Model\Author\CreateBookRequest;
use App\Model\Author\PublishBookRequest;
use App\Model\Author\UpdateBookChapterRequest;
use App\Model\Author\UpdateBookChapterSortRequest;
use App\Model\Author\UpdateBookRequest;
use App\Model\Author\UploadCoverResponse;
use App\Model\BookChapterTreeResponse;
use App\Model\ErrorResponse;
use App\Model\IdResponse;
use App\Security\Voter\AuthorBookVoter;
use App\Service\AuthorBookChapterService;
use App\Service\AuthorBookService;
use App\Service\BookPublishService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

class AuthorController extends AbstractController
{
    public function __construct(
        private AuthorBookService $authorService,
        private BookPublishService $bookPublishService,
        private AuthorBookChapterService $bookChapterService
    ) {
    }

    #[OA\Tag(name: 'Author API')]
    #[OA\Response(
        response: 200,
        description: 'Upload book cover',
        content: new Model(type: UploadCoverResponse::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation failed',
        content: new Model(type: ErrorResponse::class)
    )]
    #[Route('/api/v1/author/book/{id}/uploaderCover', methods: 'POST')]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'id')]
    public function uploadCover(
        int $id,
        #[RequestFile(
            field: 'cover',
            constraints: [
                new NotNull(),
                new Image(maxSize: '1M', mimeTypes: ['image/jpeg', 'image/png', 'image/jpg']),
            ]
        )] UploadedFile $file): Response
    {
        return $this->json($this->authorService->uploadCover($id, $file));
    }

    #[OA\Tag(name: 'Author API')]
    #[OA\Response(
        response: 200,
        description: 'Publish a book'
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation failed',
        content: new Model(type: ErrorResponse::class)
    )]
    #[OA\RequestBody(content: new Model(type: PublishBookRequest::class))]
    #[Route('/api/v1/author/book/{id}/publish', methods: 'POST')]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'id')]
    public function publish(int $id, #[RequestBody] PublishBookRequest $request): Response
    {
        $this->bookPublishService->publish($id, $request);

        return $this->json(null);
    }

    #[OA\Tag(name: 'Author API')]
    #[OA\Response(
        response: 200,
        description: 'Unpublish a book'
    )]
    #[OA\RequestBody(content: new Model(type: PublishBookRequest::class))]
    #[Route('/api/v1/author/book/{id}/unpublish', methods: 'POST')]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'id')]
    public function unpublish(int $id): Response
    {
        $this->bookPublishService->unpublish($id);

        return $this->json(null);
    }

    #[OA\Tag(name: 'Author API')]
    #[OA\Response(
        response: 200,
        description: 'Get authors owned books',
        content: new Model(type: BookListResponse::class)
    )]
    #[Route('/api/v1/author/books', methods: 'GET')]
    public function books(#[CurrentUser] UserInterface $user): Response
    {
        return $this->json($this->authorService->getBooks($user));
    }

    #[OA\Tag(name: 'Author API')]
    #[OA\Response(
        response: 200,
        description: 'Create a book',
        content: new Model(type: IdResponse::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation failed',
        content: new Model(type: ErrorResponse::class)
    )]
    #[OA\RequestBody(content: new Model(type: CreateBookRequest::class))]
    #[Route('/api/v1/author/book', methods: 'POST')]
    public function createBook(#[RequestBody] CreateBookRequest $request, #[CurrentUser] UserInterface $user): Response
    {
        return $this->json($this->authorService->createBook($request, $user));
    }

    #[OA\Tag(name: 'Author API')]
    #[OA\Response(
        response: 200,
        description: 'Remove a book',
        content: new Model(type: BookListResponse::class)
    )]
    #[OA\Response(
        response: 404,
        description: 'Book not found',
        content: new Model(type: ErrorResponse::class)
    )]
    #[Route('/api/v1/author/book/{id}', methods: 'DELETE')]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'id')]
    public function deleteBook(int $id): Response
    {
        $this->authorService->deleteBook($id);

        return $this->json(null);
    }

    #[OA\Tag(name: 'Author API')]
    #[OA\Response(
        response: 200,
        description: 'Update a book',
        content: new Model(type: BookListResponse::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation failed',
        content: new Model(type: ErrorResponse::class)
    )]
    #[OA\RequestBody(content: new Model(type: UpdateBookRequest::class))]
    #[Route('/api/v1/author/book/{id}', methods: 'POST')]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'id')]
    public function updateBook(int $id, #[RequestBody] UpdateBookRequest $request): Response
    {
        $this->authorService->updateBook($id, $request);

        return $this->json(null);
    }

    #[OA\Tag(name: 'Author API')]
    #[OA\Response(
        response: 200,
        description: 'Get authors owned books',
        content: new Model(type: BookDetails::class)
    )]
    #[OA\Response(
        response: 404,
        description: 'Book not found',
        content: new Model(type: ErrorResponse::class)
    )]
    #[Route('/api/v1/author/book/{id}', methods: 'GET')]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'id')]
    public function book(int $id): Response
    {
        return $this->json($this->authorService->getBook($id));
    }

    #[OA\Tag(name: 'Author API')]
    #[OA\Response(
        response: 200,
        description: 'Create a book chapter',
        content: new Model(type: IdResponse::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation failed',
        content: new Model(type: ErrorResponse::class)
    )]
    #[OA\RequestBody(content: new Model(type: CreateBookChapterRequest::class))]
    #[Route('/api/v1/author/book/{bookId}/chapter', methods: 'POST')]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'bookId')]
    public function createBookChapter(#[RequestBody] CreateBookChapterRequest $request, int $bookId): Response
    {
        return $this->json($this->bookChapterService->createChapter($request, $bookId));
    }

    #[OA\Tag(name: 'Author API')]
    #[OA\Response(
        response: 200,
        description: 'Sort a book chapter'
    )]
    #[OA\Response(
        response: 404,
        description: 'Book chapter not found',
        content: new Model(type: ErrorResponse::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation failed',
        content: new Model(type: ErrorResponse::class)
    )]
    #[OA\RequestBody(content: new Model(type: UpdateBookChapterSortRequest::class))]
    #[Route('/api/v1/author/book/{bookId}/chapterSort', methods: 'POST')]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'bookId')]
    public function updateBookChapterSort(#[RequestBody] UpdateBookChapterSortRequest $request, int $bookId): Response
    {
        $this->bookChapterService->updateChapterSort($request);

        return $this->json(null);
    }

    #[OA\Tag(name: 'Author API')]
    #[OA\Response(
        response: 200,
        description: 'Update a book chapter'
    )]
    #[OA\Response(
        response: 404,
        description: 'Book chapter not found',
        content: new Model(type: ErrorResponse::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation failed',
        content: new Model(type: ErrorResponse::class)
    )]
    #[OA\RequestBody(content: new Model(type: UpdateBookChapterRequest::class))]
    #[Route('/api/v1/author/book/{bookId}/updateChapter', methods: 'POST')]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'bookId')]
    public function updateBookChapter(#[RequestBody] UpdateBookChapterRequest $request, int $bookId): Response
    {
        $this->bookChapterService->updateChapter($request);

        return $this->json(null);
    }

    #[OA\Tag(name: 'Author API')]
    #[OA\Response(
        response: 200,
        description: 'Get book chapter as tree',
        content: new Model(type: BookChapterTreeResponse::class)
    )]
    #[Route('/api/v1/author/book/{bookId}/chapters', methods: 'GET')]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'bookId')]
    public function chapters(int $bookId): Response
    {
        return $this->json($this->bookChapterService->getChaptersTree($bookId));
    }

    #[OA\Tag(name: 'Author API')]
    #[OA\Response(
        response: 200,
        description: 'Remove a book chapter',
        content: new Model(type: BookListResponse::class)
    )]
    #[OA\Response(
        response: 404,
        description: 'Book chapter not found',
        content: new Model(type: ErrorResponse::class)
    )]
    #[Route('/api/v1/author/book/{bookId}/chapters/{id}', methods: 'DELETE')]
    #[IsGranted(AuthorBookVoter::IS_AUTHOR, subject: 'bookId')]
    public function deleteBookChapter(int $id, int $bookId): Response
    {
        $this->bookChapterService->deleteChapter($id);

        return $this->json(null);
    }
}
