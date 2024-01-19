<?php

namespace App\Repository;

use App\Entity\Address;
use App\Entity\User;
use App\Entity\UserData;
use DateTimeImmutable;
use Symfony\Component\Config\Definition\Exception\Exception;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserData>
 *
 * @method UserData|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserData|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserData[]    findAll()
 * @method UserData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserData::class);
    }

    public function addUserData(array $userData, User $existingUser)
    {
        $user = new UserData();
        if (array_key_exists('firstName', $userData) && !empty($userData['firstName'])) {
            $user->setFirstName($userData['firstName']);
        }
        if (array_key_exists('surname', $userData) && !empty($userData['surname'])) {
            $user->setSurname($userData['surname']);
        }
        if (array_key_exists('dateOfBirth', $userData) && !empty($userData['dateOfBirth'])) {
            $dateOfBirth = new DateTimeImmutable($userData['dateOfBirth']);
            $user->setDateOfBirth($dateOfBirth);
        }
        if (array_key_exists('phoneNumber', $userData) && !empty($userData['phoneNumber'])) {
            $user->setPhoneNumber($userData['phoneNumber']);
        }
        if (array_key_exists('mainAddress', $userData) && array_key_exists('street', $userData['mainAddress'])
            && array_key_exists('parcelNumber', $userData['mainAddress']) && array_key_exists('postcode', $userData['mainAddress'])
            && array_key_exists('city', $userData['mainAddress']) && !empty($userData['mainAddress']['street'])
            && !empty($userData['mainAddress']['parcelNumber']) && !empty($userData['mainAddress']['postcode']) && !empty($userData['mainAddress']['city']))
        {
            $mainAddress = new Address();
            $mainAddress->setStreet($userData['mainAddress']['street']);
            $mainAddress->setParcelNumber($userData['mainAddress']['parcelNumber']);
            if (array_key_exists('apartmentNumber', $userData['mainAddress']) && !empty($userData['mainAddress']['apartmentNumber'])) {
                $mainAddress->setApartmentNumber($userData['mainAddress']['apartmentNumber']);
            }
            $mainAddress->setPostcode($userData['mainAddress']['postcode']);
            $mainAddress->setCity($userData['mainAddress']['city']);
            $user->setMainAddress($mainAddress);
        } else {
            return 0;
        }
        if (array_key_exists('contactAddress', $userData) && array_key_exists('street', $userData['contactAddress'])
            && array_key_exists('parcelNumber', $userData['contactAddress']) && array_key_exists('postcode', $userData['contactAddress'])
            && array_key_exists('city', $userData['contactAddress']) && !empty($userData['contactAddress']['street'])
            && !empty($userData['contactAddress']['parcelNumber']) && !empty($userData['contactAddress']['postcode']) && !empty($userData['contactAddress']['city']))
        {
            $contactAddress = new Address();
            $contactAddress->setStreet($userData['contactAddress']['street']);
            $contactAddress->setParcelNumber($userData['contactAddress']['parcelNumber']);
            if (array_key_exists('apartmentNumber', $userData['contactAddress']['apartmentNumber']) && !empty($userData['mainAddress']['apartmentNumber'])) {
                $contactAddress->setApartmentNumber($userData['contactAddress']['apartmentNumber']);
            }
            $contactAddress->setPostcode($userData['contactAddress']['postcode']);
            $contactAddress->setCity($userData['contactAddress']['city']);
            $user->setContactAddress($contactAddress);
        }
        if (!empty($existingUser)) {
            $user->setIdUser($existingUser);
        } else {
            return 1;
        }

        $this->getEntityManager()->persist($user);
        try {
            $this->getEntityManager()->flush();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return null;
    }

    public function getUserAddressById($id): ?array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('a.street, a.parcelNumber, a.apartmentNumber, a.postcode, a.city')
            ->from('App:UserData','ud')
            ->leftJoin('ud.mainAddress', 'a')
            ->leftJoin('ud.idUser', 'u')
            ->where('u.id = :id')
            ->setParameter(':id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getDataByUserId($id)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('ud.id, a.street, a.parcelNumber, a.apartmentNumber, a.postcode, a.city')
            ->from('App:UserData','ud')
            ->leftJoin('ud.mainAddress', 'a')
            ->leftJoin('ud.idUser', 'u')
            ->where('u.id = :id')
            ->setParameter(':id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getUserDataByUserId($id)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('ud')
            ->from('App:UserData','ud')
            ->leftJoin('ud.mainAddress', 'a')
            ->leftJoin('ud.idUser', 'u')
            ->where('u.id = :id')
            ->setParameter(':id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return UserData[] Returns an array of UserData objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserData
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
