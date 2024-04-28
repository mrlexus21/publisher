<?php

namespace App\Controller;

use App\Attribute\RequestBody;
use App\Model\ErrorResponse;
use App\Model\SignUpRequest;
use App\Service\SignUpService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AuthController extends AbstractController
{
    public function __construct(private SignUpService $signUpService)
    {
    }

    #[OA\Response(
        response: 200,
        description: 'Sign up a user',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'token', type: 'string'),
            new OA\Property(property: 'refresh_token', type: 'string'),
        ])
    )]
    #[OA\Response(
        response: 409,
        description: 'User already exist',
        content: new Model(type: ErrorResponse::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation failed',
        content: new Model(type: ErrorResponse::class)
    )]
    #[OA\RequestBody(content: new Model(type: SignUpRequest::class))]
    #[Route('/api/v1/auth/signUp', methods: ['POST'])]
    public function signUp(#[RequestBody] SignUpRequest $signUpRequest): Response
    {
        return $this->signUpService->signUp($signUpRequest);
    }
}
