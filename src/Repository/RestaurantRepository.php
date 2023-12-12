<?php

namespace App\Repository;

use App\Entity\Restaurant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function MyNamespace\empty1;

/**
 * @extends ServiceEntityRepository<Restaurant>
 *
 * @method Restaurant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Restaurant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Restaurant[]    findAll()
 * @method Restaurant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RestaurantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Restaurant::class);
    }

    /**
     * @return Restaurant[] Returns an array of Restaurant objects
     */
    public function getAllRestaurants(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('r.id, r.name, r.description, r.fileName, r.email, r.phoneNumber,
        a.street, a.apartmentNumber, a.parcelNumber, a.postcode, a.city')
            ->from('App:Restaurant','r')
            ->leftJoin('r.restaurantAddress', 'a')
            ->where('a.city = :city')
            ->setParameter('city', 'Kielce')
            ->getQuery()
            ->getResult();
    }


    public function getOneRestaurantById($id): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('r.id, r.name, r.description, r.fileName, r.email, r.phoneNumber,
        a.street, a.apartmentNumber, a.parcelNumber, a.postcode, a.city')
            ->from('App:Restaurant','r')
            ->leftJoin('r.restaurantAddress', 'a')
            ->where('r.id = :id')
            ->setParameter(':id', $id)
            ->getQuery()
            ->getResult();
    }

    public function getOneRestaurantByIdToEdit($id): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb2 = $this->getEntityManager()->createQueryBuilder();
        $qb3 = $this->getEntityManager()->createQueryBuilder();
        $restaurantDb = $qb->select('r.id, r.name, r.description, r.fileName, r.email, r.phoneNumber, 
        ud.id as userId, a.street, a.apartmentNumber, a.parcelNumber, a.postcode, a.city')
            ->from('App:Restaurant','r')
            ->leftJoin('r.restaurantAddress', 'a')
            ->leftJoin('r.owner', 'ud')
            ->where('r.id = :id')
            ->setParameter(':id', $id)
            ->getQuery()
            ->getResult();
        if (intval($restaurantDb[0]['userId'])>0) {
            $ownerId = $restaurantDb[0]['userId'];
        } else {
            return [];
        }
        $ownerTmp = $qb2->select('ud.firstName, ud.surname, ud.phoneNumber, ud.dateOfBirth,
        u.email, a.street, a.apartmentNumber, a.parcelNumber, a.postcode, a.city, 
        a2.street, a2.apartmentNumber, a2.parcelNumber, a2.postcode, a2.city')
            ->from('App:UserData','ud')
            ->leftJoin('ud.idUser', 'u')
            ->leftJoin('ud.mainAddress', 'a')
            ->leftJoin('ud.mainAddress', 'a2')
            ->where('ud.id = :id')
            ->setParameter(':id', $ownerId)
            ->getQuery()
            ->getResult();

        $owner = [
            'firstName'=> $ownerTmp[0]['firstName'],
            'surname'=> $ownerTmp[0]['surname'],
            'phoneNumber'=> $ownerTmp[0]['phoneNumber'],
            'dateOfBirth'=> $ownerTmp[0]['dateOfBirth'],
            'email'=> $ownerTmp[0]['email'],
            'password'=> null,
            'mainAddress'=> [
                'street' => $ownerTmp[0]['street'],
                'parcelNumber' => $ownerTmp[0]['parcelNumber'],
                'apartmentNumber' => $ownerTmp[0]['apartmentNumber'],
                'postcode' => $ownerTmp[0]['postcode'],
                'city' => $ownerTmp[0]['city']
            ],
            'contactAddress' => null
        ];

        $restaurantCategories = $qb3->select('rc.id, c.id as categoryId, c.name, c.description')
            ->from('App:RestaurantCategory','rc')
            ->leftJoin('rc.category', 'c')
            ->where('rc.restaurant = :id')
            ->setParameter(':id', $id)
            ->getQuery()
            ->getResult();
        if (empty($restaurantCategories)) {
            return [];
        }
        return [
            0 => [
                'id'=>$restaurantDb[0]['id'],
                'name'=>$restaurantDb[0]['name'],
                'description'=>$restaurantDb[0]['description'],
                'email'=>$restaurantDb[0]['email'],
                'phoneNumber'=>$restaurantDb[0]['phoneNumber'],
                'address'=> [
                    'street'=>$restaurantDb[0]['street'],
                    'parcelNumber'=>$restaurantDb[0]['parcelNumber'],
                    'apartmentNumber'=>$restaurantDb[0]['apartmentNumber'],
                    'postcode'=>$restaurantDb[0]['postcode'],
                    'city'=>$restaurantDb[0]['city']
                ],
                'restaurantCategories'=> [$restaurantCategories],
                'owner'=> [$owner]
            ]
        ];
    }

//    /**
//     * @return Restaurant[] Returns an array of Restaurant objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Restaurant
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
