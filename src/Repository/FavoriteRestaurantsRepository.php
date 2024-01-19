<?php

namespace App\Repository;

use App\Entity\FavoriteRestaurants;
use App\Entity\Restaurant;
use App\Entity\UserData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Config\Definition\Exception\Exception;

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
            ->andWhere('ud.idUser = ' . $id)
            ->getQuery()
            ->getResult();
    }

    public function addToFavorite(Restaurant $restaurant,UserData $userData)
    {
        $favoriteRestaurant = new FavoriteRestaurants();
        $favoriteRestaurant->setRestaurant($restaurant);
        $favoriteRestaurant->setUserData($userData);
        $this->getEntityManager()->persist($favoriteRestaurant);
        try {
            $this->getEntityManager()->flush();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return null;
    }

    public function removeFromFavorite(Restaurant $restaurant, UserData $userData): bool
    {
        try {
            $favorite = $this->findOneBy(['restaurant' => $restaurant, 'userData' => $userData]);

            if (!$favorite) {
                return false;
            }

            $this->getEntityManager()->remove($favorite);
            $this->getEntityManager()->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
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
