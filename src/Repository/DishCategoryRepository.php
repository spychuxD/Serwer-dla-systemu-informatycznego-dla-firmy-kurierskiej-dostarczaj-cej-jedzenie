<?php

namespace App\Repository;

use App\Entity\DishCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DishCategory>
 *
 * @method DishCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method DishCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method DishCategory[]    findAll()
 * @method DishCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DishCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DishCategory::class);
    }

//    /**
//     * @return DishCategory[] Returns an array of DishCategory objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DishCategory
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
