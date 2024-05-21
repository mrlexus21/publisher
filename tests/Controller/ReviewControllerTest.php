<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTestCase;
use App\Tests\MockUtils;

class ReviewControllerTest extends AbstractControllerTestCase
{
    public function testReviews()
    {
        $user = MockUtils::createUser();
        $this->em->persist($user);

        $book = MockUtils::createBook()
            ->setUser($user);
        $this->em->persist($book);

        $this->em->persist(MockUtils::createReview($book));

        $this->em->flush();

        $this->client->request('GET', '/api/v1/book/'.$book->getId().'/reviews');
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['items', 'rating', 'page', 'pages', 'perPage', 'total'],
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'content', 'author', 'rating', 'createdAt'],
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'content' => ['type' => 'string'],
                            'author' => ['type' => 'string'],
                            'rating' => ['type' => 'integer'],
                            'createdAt' => ['type' => 'integer'],
                        ],
                    ],
                ],
                'rating' => ['type' => 'number'],
                'page' => ['type' => 'integer'],
                'pages' => ['type' => 'integer'],
                'perPage' => ['type' => 'integer'],
                'total' => ['type' => 'integer'],
            ],
        ]);
    }
}
