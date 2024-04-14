<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use App\Entity\Review;
use App\Tests\AbstractControllerTestCase;
use Doctrine\Common\Collections\ArrayCollection;

class ReviewControllerTest extends AbstractControllerTestCase
{
    public function testReviews()
    {
        $book = $this->createBook();
        $this->createReview($book);

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

    private function createBook(): Book
    {
        $book = (new Book())
            ->setTitle('Test book')
            ->setImage('http://localhost.png')
            ->setMeap(true)
            ->setIsbn('123456')
            ->setDescription('test description')
            ->setPublicationDate(new \DateTimeImmutable())
            ->setAuthors(['Tester'])
            ->setCategories(new ArrayCollection([]))
            ->setSlug('test-book');
        $this->em->persist($book);

        return $book;
    }

    private function createReview(Book $book)
    {
        $this->em->persist((new Review())
            ->setAuthor('tester')
            ->setContent('test content')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setRating(5)
            ->setBook($book)
        );
    }
}
