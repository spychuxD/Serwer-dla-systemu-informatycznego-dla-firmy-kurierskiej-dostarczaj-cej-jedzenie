<?php

namespace App\Repository;

use App\Entity\OpeningHoursRestaurant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OpeningHoursRestaurant>
 *
 * @method OpeningHoursRestaurant|null find($id, $lockMode = null, $lockVersion = null)
 * @method OpeningHoursRestaurant|null findOneBy(array $criteria, array $orderBy = null)
 * @method OpeningHoursRestaurant[]    findAll()
 * @method OpeningHoursRestaurant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OpeningHoursRestaurantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OpeningHoursRestaurant::class);
    }

//    /**
//     * @return OpeningHoursRestaurant[] Returns an array of OpeningHoursRestaurant objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?OpeningHoursRestaurant
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
