<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTestCase;
use App\Tests\MockUtils;
use Doctrine\Common\Collections\ArrayCollection;

class BookControllerTest extends AbstractControllerTestCase
{
    public function testBooksByCategory()
    {
        $user = MockUtils::createUser();
        $this->em->persist($user);

        $bookCategory = MockUtils::createBookCategory();
        $this->em->persist($bookCategory);

        $book = MockUtils::createBook()
            ->setCategories(new ArrayCollection([$bookCategory]))
            ->setUser($user);
        $this->em->persist($book);

        $this->em->flush();

        $this->client->request('GET', '/api/v1/category/'.$bookCategory->getId().'/books');
        $responseContent = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['items'],
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'title', 'slug', 'image', 'authors', 'publicationDate'],
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'id' => ['type' => 'integer'],
                            'image' => ['type' => 'string'],
                            'authors' => ['type' => 'array'],
                            'publicationDate' => ['type' => 'integer'],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testBookById(): void
    {
        $user = MockUtils::createUser();
        $this->em->persist($user);

        $bookCategory = MockUtils::createBookCategory();
        $this->em->persist($bookCategory);

        $format = MockUtils::createBookFormat();
        $this->em->persist($format);

        $book = MockUtils::createBook()
            ->setCategories(new ArrayCollection([$bookCategory]))
            ->setUser($user);

        $this->em->persist($book);
        $this->em->persist(MockUtils::createBookFormatLink($book, $format));
        $this->em->flush();

        $this->client->request('GET', '/api/v1/book/'.$book->getId());
        $responseContent = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['id', 'title', 'slug', 'image', 'authors', 'publicationDate', 'rating', 'reviews', 'categories', 'formats'],
            'properties' => [
                'id' => ['type' => 'integer'],
                'title' => ['type' => 'string'],
                'slug' => ['type' => 'string'],
                'image' => ['type' => 'string'],
                'author' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
                'publicationDate' => ['type' => 'integer'],
                'rating' => ['type' => 'number'],
                'reviews' => ['type' => 'integer'],
                'categories' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'title', 'slug'],
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'id' => ['type' => 'integer'],
                        ],
                    ],
                ],
                'formats' => ['type' => 'array'],
            ],
        ]);
    }
}
