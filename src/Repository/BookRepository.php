<?php

namespace App\Repository;

use App\Entity\Book;
use App\Exception\BookNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @return Book[]
     */
    public function findPublishedBooksByCategoryId(int $id): array
    {
        return $this->getEntityManager()->createQuery('select b from App\Entity\Book b where :category_id member of b.categories and b.publishedDate is not null')
            ->setParameter('categoryId', $id)
            ->getResult();
    }

    public function getPublishedById(int $id): Book
    {
        $book = $this->getEntityManager()->createQuery('select b from App\Entity\Book b where b.id = :id and b.publishedDate is not null')
            ->setParameter('id', $id)
            ->getOneOrNullResult();
        if (null === $book) {
            throw new BookNotFoundException();
        }

        return $book;
    }

    public function findBooksById(array $ids): array
    {
        return $this->getEntityManager()->createQuery('select b from App\Entity\Book b where b.id in (:ids) and b.publishedDate is not null')
            ->setParameter('ids', $ids)
            ->getOneOrNullResult();
    }

    public function findUserBooks(UserInterface $user): array
    {
        return $this->findBy(['user' => $user]);
    }

    public function getUserBookById(int $id, UserInterface $user): Book
    {
        $book = $this->findOneBy(['id' => $id, 'user' => $user]);
        if (null === $book) {
            throw new BookNotFoundException();
        }

        return $book;
    }

    public function existBySlug(string $slug): bool
    {
        return null !== $this->findOneBy(['slug' => $slug]);
    }
}
