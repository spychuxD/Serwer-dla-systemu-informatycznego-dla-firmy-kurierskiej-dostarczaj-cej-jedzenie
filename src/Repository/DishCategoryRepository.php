<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\DishCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\Exception\Exception;
/**
 * @extends ServiceEntityRepository<DishCategory>
 *
 * @method DishCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method DishCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method DishCategory[]    findAll()
 * @method DishCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DishCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DishCategory::class);
    }

    public function getDishCategory($id)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('dc')
            ->from('App:DishCategory','dc')
            ->where('dc.id = :id')
            ->setParameter(':id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function setCategory(array $data)
    {
        if (!array_key_exists('name', $data) || empty($data['name'])) {
            return array('message'=>'Nazwa kategorii jest wymagana');
        }
        $qb = $this->getEntityManager()->createQueryBuilder();
        $existingCategory = $qb->select('dc')
            ->from('App:DishCategory','dc')
            ->where('dc.name = :name')
            ->setParameter(':name', $data['name'])
            ->getQuery()
            ->getOneOrNullResult();
        if (!empty($existingCategory)){
            return array('message'=>'Istnieje taka kategoria w systemie');
        }
        $dishCategory = new DishCategory();
        $dishCategory->setName($data['name']);

        if (array_key_exists('description', $data) && !empty($data['description'])) {
            $dishCategory->setDescription($data['description']);
        }

        $this->getEntityManager()->persist($dishCategory);
        try {
            $this->getEntityManager()->flush();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return null;
    }

    public function getCategories(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $categories = $qb->select('dc.id, dc.name, dc.description')
            ->from('App:DishCategory','dc')
            ->getQuery()
            ->getResult();
        $result = [];
        foreach ($categories as $category) {
            $result[] = [
                'text'=>$category['name'],
                'value'=>$category['id']
            ];
        }
        return $result;
    }

//    /**
//     * @return DishCategory[] Returns an array of DishCategory objects
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

//    public function findOneBySomeField($value): ?DishCategory
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
