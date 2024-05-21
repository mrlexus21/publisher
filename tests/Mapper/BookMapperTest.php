<?php

namespace App\Tests\Mapper;

use App\Entity\Book;
use App\Mapper\BookMapper;
use App\Model\BookDetails;
use App\Tests\AbstractTestCase;

class BookMapperTest extends AbstractTestCase
{
    public function testMap(): void
    {
        $book = (new Book())
            ->setTitle('title')
            ->setSlug('slug')
            ->setImage('image')
            ->setAuthors(['tester'])
            ->setPublicationDate(new \DateTimeImmutable('2020-10-10'));

        $this->setEntityId($book, 1);

        $expected = (new BookDetails())
            ->setId(1)
            ->setTitle('title')
            ->setSlug('slug')
            ->setImage('image')
            ->setAuthors(['tester'])
            ->setPublicationDate((new \DateTimeImmutable('2020-10-10'))->getTimestamp());

        $this->assertEquals($expected, BookMapper::map($book, new BookDetails()));
    }
}
