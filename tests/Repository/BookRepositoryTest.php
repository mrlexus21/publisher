<?php

namespace App\Tests\Repository;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Repository\BookRepository;
use App\Tests\AbstractRepositoryTestCase;
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
        $devicesCategory = (new BookCategory())->setTitle('Devices')->setSlug('devices');
        $this->em->persist($devicesCategory);

        for ($i = 0; $i < 5; ++$i) {
            $book = $this->createBook('device-'.$i, [$devicesCategory]);
            $this->em->persist($book);
        }

        $this->em->flush();

        $this->assertCount(5, $this->bookRepository->findBooksByCategoryId($devicesCategory->getId()));
    }

    private function createBook(string $title, array $category): Book
    {
        return (new Book())
            ->setPublicationDate(new \DateTimeImmutable())
            ->setAuthors(['author'])
            ->setMeap(false)
            ->setIsbn('123456')
            ->setDescription('test description')
            ->setSlug($title)
            ->setCategories(new ArrayCollection($category))
            ->setTitle($title)
            ->setImage('http://locachost/'.$title.'.png');
    }
}
