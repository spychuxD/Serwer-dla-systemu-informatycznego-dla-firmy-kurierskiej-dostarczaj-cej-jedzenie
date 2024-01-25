<?php

namespace App\Repository;

use App\Entity\Messages;
use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\Exception\Exception;
/**
 * @extends ServiceEntityRepository<Messages>
 *
 * @method Messages|null find($id, $lockMode = null, $lockVersion = null)
 * @method Messages|null findOneBy(array $criteria, array $orderBy = null)
 * @method Messages[]    findAll()
 * @method Messages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Messages::class);
    }

    public function getMessagesByOrderId($id): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('m')
            ->from('App:Messages','m')
            ->leftJoin('m.orderId', 'o')
            ->andWhere('o.id = ' . $id)
            ->orderBy('m.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function addMessage($messageTo, $sender, Order $order, $date)
    {
        $message = new Messages();
        $message->setMessage($messageTo);
        $message->setSender($sender);
        $message->setOrderId($order);
        $message->setDate(new \DateTime($date));

        $this->getEntityManager()->persist($message);
        try {
            $this->getEntityManager()->flush();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return null;
    }

//    /**
//     * @return Messages[] Returns an array of Messages objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Messages
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
