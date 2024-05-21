<?php

namespace App\Tests\Repository;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Tests\AbstractRepositoryTestCase;
use App\Tests\MockUtils;
use Doctrine\Common\Collections\ArrayCollection;

class BookRepositoryTest extends AbstractRepositoryTestCase
{
    private BookRepository $bookRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookRepository = $this->getRepositoryForEntity(Book::class);
    }

    public function testFindBooksByCategoryId()
    {
        $user = MockUtils::createUser();
        $this->em->persist($user);

        $devicesCategory = MockUtils::createBookCategory();
        $this->em->persist($devicesCategory);

        for ($i = 0; $i < 5; ++$i) {
            $book = MockUtils::createBook()->setUser($user)
                ->setTitle('device-'.$i)
                ->setCategories(new ArrayCollection([$devicesCategory]));
            $this->em->persist($book);
        }
        $this->em->flush();

        $this->assertCount(5, $this->bookRepository->findPublishedBooksByCategoryId($devicesCategory->getId()));
    }
}
