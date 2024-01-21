<?php
namespace App\Repository;

use App\Entity\RestaurantRatings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
class RestaurantRatingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, RestaurantRatings::class);
        $this->entityManager = $entityManager;
    }

    public function addRating(RestaurantRatings $rating)
    {
        $this->entityManager->persist($rating);
        try {
            $this->entityManager->flush();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return null;
    }

    public function removeRating(RestaurantRatings $rating)
    {
        $this->entityManager->remove($rating);
        try {
            $this->entityManager->flush();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return null;
    }

    public function getAllRatings(): array
    {
        return $this->findAll();
    }

    public function getRatingById($id): ?RestaurantRatings
    {
        return $this->find($id);
    }

    public function getUserRatings($id): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('rr')
            ->from('App:RestaurantRatings','rr')
            ->leftJoin('rr.restaurant', 'r')
            ->leftJoin('r.restaurantAddress', 'a')
            ->leftJoin('rr.userData', 'ud')
            ->andWhere('ud.idUser = ' . $id)
            ->getQuery()
            ->getResult();
    }
    public function getRestaurantRatings($id): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('rr')
            ->from('App:RestaurantRatings','rr')
            ->leftJoin('rr.restaurant', 'r')
            ->leftJoin('r.restaurantAddress', 'a')
            ->leftJoin('rr.userData', 'ud')
            ->andWhere('r.id = ' . $id)
            ->getQuery()
            ->getResult();
    }
}

