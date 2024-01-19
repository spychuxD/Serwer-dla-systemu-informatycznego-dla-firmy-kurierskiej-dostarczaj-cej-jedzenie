<?php

namespace App\Repository;

use App\Entity\Courier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\Exception\Exception;
/**
 * @extends ServiceEntityRepository<Courier>
 *
 * @method Courier|null find($id, $lockMode = null, $lockVersion = null)
 * @method Courier|null findOneBy(array $criteria, array $orderBy = null)
 * @method Courier[]    findAll()
 * @method Courier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Courier::class);
    }

    public function getCourierById($id): ?Courier
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('c')
            ->from('App:Courier','c')
            ->leftJoin('c.userData', 'ud')
            ->leftJoin('ud.idUser', 'u')
            ->where('u.id = :id')
            ->setParameter(':id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getCourierByIdUser($id): ?Courier
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('c')
            ->from('App:Courier','c')
            ->leftJoin('c.userData', 'ud')
            ->leftJoin('ud.idUser', 'u')
            ->where('u.id = :id')
            ->setParameter(':id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getCourierNotAssigned()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('c')
            ->from('App:Courier','c')
//            ->leftJoin('r.restaurantAddress', 'a')
            ->where('c.assigned = false')
//            ->setParameter(':id', $id)
            ->getQuery()
            ->getResult();
    }

    public function updateCourierAssigned(Courier $courier) {
        $this->getEntityManager()->persist($courier);
        try {
            $this->getEntityManager()->flush();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return null;
    }

//    /**
//     * @return Courier[] Returns an array of Courier objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Courier
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
