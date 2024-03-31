<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Tests\AbstractControllerTestCase;
use Doctrine\Common\Collections\ArrayCollection;

class BookControllerTestCase extends AbstractControllerTestCase
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
                        'required' => ['id', 'title', 'slug', 'image', 'authors', 'meap', 'publicationData'],
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'id' => ['type' => 'integer'],
                            'image' => ['type' => 'string'],
                            'authors' => ['type' => 'array'],
                            'meap' => ['type' => 'boolean'],
                            'publicationData' => ['type' => 'integer'],
                        ],
                    ],
                ],
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
            ->setPublicationDate(new \DateTime())
            ->setAuthors(['Tester'])
            ->setCategories(new ArrayCollection([$bookCategory]))
            ->setSlug('test-book')
        );

        $this->em->flush();

        return $bookCategory->getId();
    }
}
