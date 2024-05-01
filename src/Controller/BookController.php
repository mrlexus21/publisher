<?php

namespace App\Controller;

use App\Model\BookDetails;
use App\Model\BookListResponse;
use App\Model\ErrorResponse;
use App\Service\BookService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookController extends AbstractController
{
    public function __construct(private BookService $bookService)
    {
    }

    #[OA\Response(
        response: 200,
        description: 'Returns published books inside a category',
        content: new Model(type: BookListResponse::class)
    )]
    #[OA\Response(
        response: 404,
        description: 'Book category not found',
        content: new Model(type: ErrorResponse::class)
    )]
    #[Route('/api/v1/category/{id}/books', methods: ['GET'])]
    public function booksByCategory(int $id): Response
    {
        return $this->json($this->bookService->getBooksByCategory($id));
    }

    #[OA\Response(
        response: 200,
        description: 'Returns published book detail information',
        content: new Model(type: BookDetails::class)
    )]
    #[OA\Response(
        response: 404,
        description: 'Book not found',
        content: new Model(type: ErrorResponse::class)
    )]
    #[Route('/api/v1/book/{id}', methods: ['GET'])]
    public function booksById(int $id): Response
    {
        return $this->json($this->bookService->getBookById($id));
    }
}
