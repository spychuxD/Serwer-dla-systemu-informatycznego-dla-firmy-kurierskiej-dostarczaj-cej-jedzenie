<?php

namespace App\Repository;

use App\Entity\IngridientCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @extends ServiceEntityRepository<IngridientCategory>
 *
 * @method IngridientCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method IngridientCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method IngridientCategory[]    findAll()
 * @method IngridientCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IngridientCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IngridientCategory::class);
    }

    public function setCategory(array $data)
    {
        if (!array_key_exists('name', $data) || empty($data['name'])) {
            return array('message'=>'Nazwa kategorii jest wymagana');
        }
        $qb = $this->getEntityManager()->createQueryBuilder();
        $existingCategory = $qb->select('ic')
            ->from('App:IngridientCategory','ic')
            ->where('ic.name = :name')
            ->setParameter(':name', $data['name'])
            ->getQuery()
            ->getOneOrNullResult();
        if (!empty($existingCategory)){
            return array('message'=>'Istnieje taka kategoria w systemie');
        }
        $ingridientCategory = new IngridientCategory();
        $ingridientCategory->setName($data['name']);

        if (array_key_exists('description', $data) && !empty($data['description'])) {
            $ingridientCategory->setDescription($data['description']);
        }
        $ingridientCategory->setIsMultiOption($data['isMultiOption']);

        $this->getEntityManager()->persist($ingridientCategory);
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
        $categories = $qb->select('ic.id, ic.name, ic.description')
            ->from('App:IngridientCategory','ic')
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
//     * @return IngridientCategory[] Returns an array of IngridientCategory objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?IngridientCategory
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
