<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Entity\BookFormat;
use App\Entity\BookToBookFormat;
use App\Tests\AbstractControllerTestCase;
use Doctrine\Common\Collections\ArrayCollection;

class BookControllerTest extends AbstractControllerTestCase
{
    public function testBooksByCategory()
    {
        $categoryId = $this->createCategory();

        $this->client->request('GET', '/api/v1/category/'.$categoryId.'/books');
        $responseContent = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['items'],
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'title', 'slug', 'image', 'authors', 'meap', 'publicationDate'],
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'id' => ['type' => 'integer'],
                            'image' => ['type' => 'string'],
                            'authors' => ['type' => 'array'],
                            'meap' => ['type' => 'boolean'],
                            'publicationDate' => ['type' => 'integer'],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testBookById(): void
    {
        $bookId = $this->createBook();
        $this->client->request('GET', '/api/v1/book/'.$bookId);
        $responseContent = $this->client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['id', 'title', 'slug', 'image', 'authors', 'meap', 'publicationDate', 'rating', 'reviews', 'categories', 'formats'],
            'properties' => [
                'id' => ['type' => 'integer'],
                'title' => ['type' => 'string'],
                'slug' => ['type' => 'string'],
                'image' => ['type' => 'string'],
                'author' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
                'meap' => ['type' => 'boolean'],
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

    private function createCategory(): int
    {
        $bookCategory = (new BookCategory())->setTitle('Devices')->setSlug('devices');
        $this->em->persist($bookCategory);

        $this->em->persist((new Book())
            ->setTitle('Test book')
            ->setImage('http://localhost.png')
            ->setMeap(true)
            ->setIsbn('123456')
            ->setDescription('test description')
            ->setPublicationDate(new \DateTimeImmutable())
            ->setAuthors(['Tester'])
            ->setCategories(new ArrayCollection([$bookCategory]))
            ->setSlug('test-book')
        );

        $this->em->flush();

        return $bookCategory->getId();
    }

    private function createBook(): int
    {
        $bookCategory = (new BookCategory())->setTitle('Devices')->setSlug('devices');
        $this->em->persist($bookCategory);

        $format = (new BookFormat())
            ->setTitle('format')
            ->setDescription('description format')
            ->setComment(null);
        $this->em->persist($format);

        $book = (new Book())
            ->setTitle('Test book')
            ->setImage('http://localhost.png')
            ->setMeap(true)
            ->setIsbn('123456')
            ->setDescription('test description')
            ->setPublicationDate(new \DateTimeImmutable())
            ->setAuthors(['Tester'])
            ->setCategories(new ArrayCollection([$bookCategory]))
            ->setSlug('test-book');
        $this->em->persist($book);

        $join = (new BookToBookFormat())
            ->setBook($book)
            ->setFormat($format)
            ->setPrice(123.55)
            ->setDiscountPercent(5);
        $this->em->persist($join);

        $this->em->flush();

        return $book->getId();
    }
}
