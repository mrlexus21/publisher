<?php

namespace App\Controller;

use App\Model\ReviewPage;
use App\Service\ReviewService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReviewController extends AbstractController
{
    public function __construct(private ReviewService $reviewService)
    {
    }

    #[OA\Parameter(name: 'page', description: 'Page number', in: 'query', schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(
        response: 200,
        description: 'Returns page of reviews for the given book',
        content: new Model(type: ReviewPage::class)
    )]
    #[Route('/api/v1/book/{id}/reviews', methods: ['GET'])]
    public function reviews(int $id, Request $request): Response
    {
        return $this->json($this->reviewService->getReviewPageByBookId($id, $request->query->get('page', 1)));
    }
}
