<?php

namespace App\Repository;

use App\Entity\FavoriteRestaurants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FavoriteRestaurants>
 *
 * @method FavoriteRestaurants|null find($id, $lockMode = null, $lockVersion = null)
 * @method FavoriteRestaurants|null findOneBy(array $criteria, array $orderBy = null)
 * @method FavoriteRestaurants[]    findAll()
 * @method FavoriteRestaurants[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FavoriteRestaurantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FavoriteRestaurants::class);
    }
    /**
     * @return FavoriteRestaurants[] Returns an array of FavoriteRestaurants objects
     */
    public function getFavoriteRestaurantsByUserId($id): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('r.id, r.name, r.description, r.fileName, r.email, r.phoneNumber,
        a.street, a.apartmentNumber, a.parcelNumber, a.postcode, a.city')
            ->from('App:FavoriteRestaurants','fr')
            ->leftJoin('fr.restaurant', 'r')
            ->leftJoin('r.restaurantAddress', 'a')
            ->leftJoin('fr.userData', 'ud')
            ->where('a.city = :city')
            ->setParameter('city', 'Kielce')
            ->andWhere('ud.idUser = ' . $id)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return FavoriteRestaurants[] Returns an array of FavoriteRestaurants objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FavoriteRestaurants
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
