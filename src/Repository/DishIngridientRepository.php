<?php

namespace App\Repository;

use App\Entity\DishIngridient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DishIngridient>
 *
 * @method DishIngridient|null find($id, $lockMode = null, $lockVersion = null)
 * @method DishIngridient|null findOneBy(array $criteria, array $orderBy = null)
 * @method DishIngridient[]    findAll()
 * @method DishIngridient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DishIngridientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DishIngridient::class);
    }

    public function getAllByDishId($id): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('i.id as ingridientId, i.name as ingridientName, i.description as ingridientDescription,
        i.price, ic.name as ingridientCategoryName, ic.description as ingridientCategoryDescription, ic.isMultiOption')
            ->from('App:DishIngridient','di')
            ->leftJoin('di.ingridient', 'i')
            ->leftJoin('i.ingridientCategory', 'ic')
            ->andWhere('di.dish = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return DishIngridient[] Returns an array of DishIngridient objects
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

//    public function findOneBySomeField($value): ?DishIngridient
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
