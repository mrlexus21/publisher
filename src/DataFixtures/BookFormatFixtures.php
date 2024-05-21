<?php

namespace App\DataFixtures;

use App\Entity\BookFormat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BookFormatFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $format1 = (new BookFormat())
            ->setTitle('eBook')
            ->setDescription('Eliminate the unavoidable complexity of object-oriented designs. The innovative data-oriented programming paradigm makes your systems less complex by making it simpler to access and manipulate data.')
            ->setComment(null);

        $format2 = (new BookFormat())
            ->setTitle('print + eBook')
            ->setDescription('Data-Oriented Programming is a one-of-a-kind guide that introduces the data-oriented paradigm.')
            ->setComment('This groundbreaking approach represents data');

        $manager->persist($format1);
        $manager->persist($format2);
        $manager->flush();
    }
}
