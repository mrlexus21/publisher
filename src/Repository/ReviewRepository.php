<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 *
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function countByBookId(int $id): int
    {
        return $this->count(['book' => $id]);
    }

    public function getBookTotalRatingSum(int $id): int
    {
        return (int) $this->getEntityManager()->createQuery('select sum(r.rating) from App\Entity\Review r where r.book=:id')
            ->setParameter('id', $id)
            ->getSingleScalarResult();
    }

    public function getPageByBookId(int $id, int $offset, int $limit): Paginator
    {
        $query = $this->getEntityManager()->createQuery('select r from App\Entity\Review r where r.book=:id order by r.createdAt desc')
            ->setParameter('id', $id)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return new Paginator($query, false);
    }
}
