<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\Exception\Exception;
/**
 * @extends ServiceEntityRepository<Order>
 *
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }
    public function addOrder(Order $order) {
        $this->getEntityManager()->persist($order);
        try {
            $this->getEntityManager()->flush();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return null;
    }

    public function updateOrderStatus(Order $order) {
        $this->getEntityManager()->persist($order);
        try {
            $this->getEntityManager()->flush();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return null;
    }

    public function getNewOrderForCourier($id)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('o.id, o.address, o.status, o.cost,
        CONCAT(a.street, \', \', a.parcelNumber, \', \', a.apartmentNumber) AS address_line_1, a.city, a.postcode AS zip_code,
        r.lat, r.lng, r.name')
            ->from('App:Order','o')
            ->leftJoin('o.courier', 'c')
            ->andWhere('c.id = :id')
            ->leftJoin('o.restaurant', 'r')
            ->leftJoin('r.restaurantAddress', 'a')
            ->setParameter(':id', $id)
            ->andWhere('o.status = :status')
            ->setParameter(':status', 'PENDING')
            ->andWhere('c.assigned = false')
            ->getQuery()
            ->getResult();
    }
    public function getOrderForCourier($id)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('o.id, o.address, o.status, o.cost, o.cart,
        CONCAT(a.street, \' \', a.parcelNumber, \' / \', a.apartmentNumber) AS address_line_1, a.city, a.postcode AS zip_code,
        r.lat, r.lng, r.name')
            ->from('App:Order','o')
            ->leftJoin('o.courier', 'c')
            ->leftJoin('o.restaurant', 'r')
            ->leftJoin('r.restaurantAddress', 'a')
            ->where('c.id = :id')
            ->andWhere('c.assigned = true')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->eq('o.status', ':status1'),
                $qb->expr()->eq('o.status', ':status2')
            ))
            ->setParameter(':id', $id)
            ->setParameter(':status1', 'ACCEPTED')
            ->setParameter(':status2', 'DELIVERY')
            ->getQuery()
            ->getResult();
    }

    public function getOrderById($id): ?Order
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('o')
            ->from('App:Order','o')
            ->where('o.id = :id')
            ->setParameter(':id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getOrders(): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('o.address AS userAddress, o.cart, o.cost, o.status,
        u.email AS userEmail, ud.id AS userId, cu.email AS courierEmail, c.id AS courierId, r.name AS restaurantName, r.id AS restaurantId')
            ->from('App:Order','o')
            ->leftJoin('o.userData', 'ud')
            ->leftJoin('ud.idUser', 'u')
            ->leftJoin('o.restaurant', 'r')
            ->leftJoin('o.courier', 'c')
            ->leftJoin('c.userData', 'cud')
            ->leftJoin('cud.idUser', 'cu')
            ->getQuery()
            ->getResult();

    }

//    /**
//     * @return Order[] Returns an array of Order objects
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

//    public function findOneBySomeField($value): ?Order
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
