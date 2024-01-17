<?php

namespace App\Repository;

use App\Entity\Dish;
use App\Entity\DishIngridient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\Exception\Exception;

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

    public function getDishes(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('d.id as dishId, d.name as dishName, d.description as dishDescription, d.price, 
        dc.id as categoryId, dc.name as categoryName, dc.description as categoryDescription, r.name as restaurantName')
            ->from('App:Dish','d')
            ->leftJoin('d.dishCategory', 'dc')
            ->leftJoin('d.restaurant', 'r')
            ->getQuery()
            ->getResult();
    }

    public function setDish(array $data)
    {
        $dish = new Dish();
        $dish->setName($data['name']);
        $dish->setDescription($data['description']);

        $dishCategoryRepository = $this->getEntityManager()->getRepository(DishCategoryRepository::class);
        $category = $dishCategoryRepository->find($data['category']['value']);
        $dish->setDishCategory($category);
        $this->getEntityManager()->persist($dish);
        try {
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        $dishCategoryRepository = $this->getEntityManager()->getRepository(DishIngridientRepository::class);
        foreach ($data['dishIngridients'] as $di) {
            $ingridient = $dishCategoryRepository->find($di['value']);
            $dishIngridient = new DishIngridient();
            $dishIngridient->setDish($dish);
            $dishIngridient->setIngridient($ingridient);
            $this->getEntityManager()->persist($dishIngridient);
            try {
                $this->getEntityManager()->flush();
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }
        return $dish;
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
