<?php

declare(strict_types=1);

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Attribute\RequestFile;
use App\Model\Author\BookListResponse;
use App\Model\Author\CreateBookRequest;
use App\Model\Author\PublishBookRequest;
use App\Model\Author\UploadCoverResponse;
use App\Model\ErrorResponse;
use App\Model\IdResponse;
use App\Service\AuthorService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

class AuthorController extends AbstractController
{
    public function __construct(
        private AuthorService $authorService
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
    public function publish(int $id, #[RequestBody] PublishBookRequest $request): Response
    {
        $this->authorService->publish($id, $request);

        return $this->json(null);
    }

    #[OA\Tag(name: 'Author API')]
    #[OA\Response(
        response: 200,
        description: 'Unpublish a book'
    )]
    #[OA\RequestBody(content: new Model(type: PublishBookRequest::class))]
    #[Route('/api/v1/author/book/{id}/unpublish', methods: 'POST')]
    public function unpublish(int $id): Response
    {
        $this->authorService->unpublish($id);

        return $this->json(null);
    }

    #[OA\Tag(name: 'Author API')]
    #[OA\Response(
        response: 200,
        description: 'Get authors owned books',
        content: new Model(type: BookListResponse::class)
    )]
    #[Route('/api/v1/author/books', methods: 'GET')]
    public function books(): Response
    {
        return $this->json($this->authorService->getBooks());
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
    public function createBook(#[RequestBody] CreateBookRequest $request): Response
    {
        return $this->json($this->authorService->createBook($request));
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
    public function deleteBook(int $id): Response
    {
        $this->authorService->deleteBook($id);

        return $this->json(null);
    }
}
