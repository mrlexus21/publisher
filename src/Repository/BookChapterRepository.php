<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\BookChapter;
use App\Exception\BookChapterNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookChapter>
 *
 * @method BookChapter|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookChapter|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookChapter[]    findAll()
 * @method BookChapter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookChapterRepository extends ServiceEntityRepository
{
    use RepositoryModifyTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookChapter::class);
    }

    public function getById(int $id): BookChapter
    {
        $bookChapter = $this->find($id);
        if (null === $bookChapter) {
            throw new BookChapterNotFoundException();
        }

        return $bookChapter;
    }

    public function getMaxSort(Book $book, int $level): int
    {
        return (int) $this->getEntityManager()->createQuery('select max(c.sort) from App\Entity\BookChapter c where c.book = :book and c.level = :level')
            ->setParameter('book', $book->getId())
            ->setParameter('level', $level)
            ->getSingleScalarResult();
    }

    public function increaseSortFrom(int $sortStart, Book $book, int $level, int $sortStep = 1): void
    {
        $sql = <<<SQL
        update App\Entity\BookChapter c
        set c.sort = c.sort + :sortStep
        where c.sort >= :sortStart and c.book = :book and c.level = :level
        SQL;

        $this->getEntityManager()->createQuery($sql)
            ->setParameter('book', $book->getId())
            ->setParameter('level', $level)
            ->setParameter('sortStart', $sortStart)
            ->setParameter('sortStep', $sortStep)
            ->execute();
    }

    public function findSortedChaptersByBook(Book $book): array
    {
        return $this->findBy(['book' => $book], ['level' => Criteria::ASC, 'sort' => Criteria::ASC]);
    }
}
