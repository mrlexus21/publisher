<?php

declare(strict_types=1);

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Model\BookCategoryUpdateRequest;
use App\Model\ErrorResponse;
use App\Model\IdResponse;
use App\Service\BookCategoryService;
use App\Service\RoleService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    public function __construct(
        private RoleService $roleService,
        private BookCategoryService $bookCategoryService
    ) {
    }

    #[OA\Tag(name: 'Admin API')]
    #[OA\Response(
        response: 200,
        description: 'Grants ROLE_AUTHOR to a user'
    )]
    #[OA\Response(
        response: 409,
        description: 'User not found',
        content: new Model(type: ErrorResponse::class)
    )]
    #[Route('/api/v1/admin/grantAuthor/{userId}', methods: ['POST'])]
    public function grantAuthor(int $userId): Response
    {
        $this->roleService->grantAuthor($userId);

        return $this->json(null);
    }

    #[OA\Tag(name: 'Admin API')]
    #[OA\Response(
        response: 200,
        description: 'Delete a book category'
    )]
    #[OA\Response(
        response: 400,
        description: 'Book category still contains books',
        content: new Model(type: ErrorResponse::class)
    )]
    #[OA\Response(
        response: 404,
        description: 'Book category not found',
        content: new Model(type: ErrorResponse::class)
    )]
    #[Route('/api/v1/admin/bookCategory/{id}', methods: ['DELETE'])]
    public function deleteCategory(int $id): Response
    {
        $this->bookCategoryService->deleteCategory($id);

        return $this->json(null);
    }

    #[OA\Tag(name: 'Admin API')]
    #[OA\Response(
        response: 200,
        description: 'Create a new category',
        content: new Model(type: IdResponse::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation failed',
        content: new Model(type: ErrorResponse::class)
    )]
    #[OA\Response(
        response: 409,
        description: 'Book category already exist',
        content: new Model(type: ErrorResponse::class)
    )]
    #[OA\RequestBody(content: new Model(type: BookCategoryUpdateRequest::class))]
    #[Route('/api/v1/admin/bookCategory', methods: ['POST'])]
    public function createCategory(#[RequestBody] BookCategoryUpdateRequest $request): Response
    {
        return $this->json($this->bookCategoryService->createCategory($request));
    }

    #[OA\Tag(name: 'Admin API')]
    #[OA\Response(
        response: 200,
        description: 'Update a new category',
        content: new Model(type: IdResponse::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation failed',
        content: new Model(type: ErrorResponse::class)
    )]
    #[OA\Response(
        response: 409,
        description: 'Book category already exist',
        content: new Model(type: ErrorResponse::class)
    )]
    #[OA\RequestBody(content: new Model(type: BookCategoryUpdateRequest::class))]
    #[Route('/api/v1/admin/bookCategory/{id}', methods: ['POST'])]
    public function updateCategory(int $id, #[RequestBody] BookCategoryUpdateRequest $request): Response
    {
        $this->bookCategoryService->updateCategory($id, $request);

        return $this->json(null);
    }
}
