<?php

namespace App\Repository;

use App\Entity\CourierWorkDays;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @extends ServiceEntityRepository<CourierWorkDays>
 *
 * @method CourierWorkDays|null find($id, $lockMode = null, $lockVersion = null)
 * @method CourierWorkDays|null findOneBy(array $criteria, array $orderBy = null)
 * @method CourierWorkDays[]    findAll()
 * @method CourierWorkDays[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourierWorkDaysRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, CourierWorkDays::class);
        $this->entityManager = $entityManager;
    }

    public function addWorkDay(CourierWorkDays $workDay)
    {
        $this->entityManager->persist($workDay);
        try {
            $this->entityManager->flush();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return null;
    }

//    public function removeCourierWorkDay(CourierWorkDays $workDay)
//    {
//        $this->entityManager->remove($workDay);
//        try {
//            $this->entityManager->flush();
//        } catch (Exception $e) {
//            return $e->getMessage();
//        }
//        return null;
//    }

    public function getAllCouriersWorkDays(): array
    {
        return $this->findAll();
    }

    public function getCourierWorkDayById($id): ?CourierWorkDays
    {
        return $this->find($id);
    }

    public function getCourierWorkDays($id): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('cwd')
            ->from('App:CourierWorkDays','cwd')
            ->leftJoin('cwd.courier', 'c')
            ->leftJoin('c.userData', 'ud')
            ->leftJoin('ud.idUser', 'u')
            ->andWhere('u.id = ' . $id)
            ->getQuery()
            ->getResult();
    }
//    public function getRestaurantRatings($id): array
//    {
//        $qb = $this->getEntityManager()->createQueryBuilder();
//        return $qb->select('rr')
//            ->from('App:RestaurantRatings','rr')
//            ->leftJoin('rr.restaurant', 'r')
//            ->leftJoin('r.restaurantAddress', 'a')
//            ->leftJoin('rr.userData', 'ud')
//            ->andWhere('r.id = ' . $id)
//            ->getQuery()
//            ->getResult();
//    }
}
