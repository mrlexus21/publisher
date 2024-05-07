<?php

namespace App\Repository;

use App\Entity\BookCategory;
use App\Exception\BookCategoryNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookCategory>
 *
 * @method BookCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookCategory[]    findAll()
 * @method BookCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookCategory::class);
    }

    /**
     * @return BookCategory[]
     */
    public function findAllSortedByTitle(): array
    {
        return $this->findBy([], ['title' => Criteria::ASC]);
    }

    public function existById(int $id): bool
    {
        return null !== $this->find($id);
    }

    public function getById(int $id): BookCategory
    {
        $category = $this->find($id);
        if (null === $category) {
            throw new BookCategoryNotFoundException();
        }

        return $category;
    }

    public function countBooksInCategory(int $id): int
    {
        return $this->getEntityManager()->createQuery('select count(b.id) from App\Entity\Book b where :categoryId member of b.categories')
            ->setParameter('categoryId', $id)
            ->getSingleScalarResult();
    }

    public function existBySlug(string $slug): bool
    {
        return null !== $this->findOneBy(['slug' => $slug]);
    }
}
