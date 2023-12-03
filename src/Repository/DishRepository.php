<?php

namespace App\Repository;

use App\Entity\Dish;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Dish>
 *
 * @method Dish|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dish|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dish[]    findAll()
 * @method Dish[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DishRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dish::class);
    }

    /**
     * @return Dish[] Returns an array of Dish objects
     */
    public function getByIdResturant($id): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('d.id as dishId, d.name as dishName, d.description as dishDescription, d.price, 
        dc.id as categoryId, dc.name as categoryName, dc.description as categoryDescription')
            ->from('App:Dish','d')
            ->leftJoin('d.dishCategory', 'dc')
            ->leftJoin('d.restaurant', 'r')
            ->andWhere('r.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getResult();
    }


//    public function findOneBySomeField($value): ?Dish
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
