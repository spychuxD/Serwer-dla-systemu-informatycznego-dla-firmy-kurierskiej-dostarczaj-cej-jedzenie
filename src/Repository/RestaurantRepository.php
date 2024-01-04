<?php

namespace App\Repository;

use App\Entity\Address;
use App\Entity\Category;
use App\Entity\OpeningHoursRestaurant;
use App\Entity\Restaurant;
use App\Entity\User;
use App\Entity\UserData;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
    private UserPasswordHasherInterface $passwordHasher;
    public function __construct(ManagerRegistry $registry, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct($registry, Restaurant::class);
        $this->passwordHasher = $passwordHasher;
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
//            ->where('a.city = :city')
//            ->setParameter('city', 'Kielce')
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

    public function getRestaurantById($id): ?Restaurant
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('r')
            ->from('App:Restaurant','r')
            ->where('r.id = :id')
            ->setParameter(':id', $id)
            ->getQuery()
            ->getOneOrNullResult();
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

    public function setRestaurant(array $data)
    {

        $restaurant = new Restaurant();
        if (!array_key_exists('name', $data) || empty($data['name'])) {
            return array('message'=>'Nazwa restauracji jest wymagana');
        }

        if (array_key_exists('description', $data) && !empty($data['description'])) {
            $restaurant->setDescription($data['description']);
        }
//        odkomentować, po uzwgledneniu dodawania plikow
//        if (!array_key_exists('fileName', $data) || empty($data['fileName'])) {
//            return 'Logo restauracji jest wymagane w celu dodania restauracji do systemu.';
//        }

        if (!array_key_exists('email', $data) || empty($data['email'])) {
            return array('message'=>'Adres e-mail restauracji jest wymagany w celu dodania restauracji do systemu.');
        }
        if (!array_key_exists('phoneNumber', $data) || empty($data['phoneNumber'])) {
            return array('message'=>'Numer telefonu restauracji jest wymagany w celu dodania restauracji do systemu.');
        }
        if (!array_key_exists('openingHoursRestaurant', $data) || empty($data['openingHoursRestaurant'])) {
            return array('message'=>'Godziny otwarcia restauracji są wymagane w celu dodania restauracji do systemu.');
        }
        if (!array_key_exists('address', $data)                    ||  empty($data['address'])
            || !array_key_exists('street', $data['address'])       ||  empty($data['address']['street'])
            || !array_key_exists('parcelNumber', $data['address']) ||  empty($data['address']['parcelNumber'])
            || !array_key_exists('postcode', $data['address'])     ||  empty($data['address']['postcode'])
            || !array_key_exists('city', $data['address'])         ||  empty($data['address']['city']))
        {
            return array('message'=>'Adres restauracji jest wymagany w celu dodania restauracji do systemu.');
        }
        //        odkomentować, po uzwgledneniu dodawania nowych kategorii
//        if (!array_key_exists('restaurantCategories', $data) || empty($data['restaurantCategories'])) {
//            return 'Kategoria restauracji jest wymagana w celu dodania restauracji do systemu.';
//        }
        if (!array_key_exists('owner', $data) || empty($data['owner']) //sprawdzanie dla wlasciciela adresu i danych - wlasciciel powinien posiadac wiekszosc informacji z encji
            || !array_key_exists('email', $data['owner'])                       ||  empty($data['owner']['email'])
            || !array_key_exists('password', $data['owner'])                    ||  empty($data['owner']['password'])
            || !array_key_exists('firstName', $data['owner'])                   ||  empty($data['owner']['firstName'])
            || !array_key_exists('surname', $data['owner'])                     ||  empty($data['owner']['surname'])
            || !array_key_exists('phoneNumber', $data['owner'])                 ||  empty($data['owner']['phoneNumber'])
            || !array_key_exists('mainAddress', $data['owner'])                 ||  empty($data['owner']['mainAddress'])
            || !array_key_exists('street', $data['owner']['mainAddress'])       ||  empty($data['owner']['mainAddress']['street'])
            || !array_key_exists('parcelNumber', $data['owner']['mainAddress']) ||  empty($data['owner']['mainAddress']['parcelNumber'])
            || !array_key_exists('postcode', $data['owner']['mainAddress'])     ||  empty($data['owner']['mainAddress']['postcode'])
            || !array_key_exists('city', $data['owner']['mainAddress'])         ||  empty($data['owner']['mainAddress']['city']))
        {
            return array('message'=>'Właściciel restauracji jest wymagany w celu dodania restauracji do systemu - należy podać wymagane dane dla nowego użytkownika.');
        }

        $restaurant->setName($data['name']);
        $restaurant->setFileName('restauracja1.jpg');
        $restaurant->setEmail($data['email']);
        $restaurant->setPhoneNumber($data['phoneNumber']);

        $address = new Address();
        $address->setStreet($data['address']['street']);
        $address->setParcelNumber($data['address']['parcelNumber']);
        if (array_key_exists('apartmentNumber', $data['address']) && !empty($data['address']['apartmentNumber'])) {
            $address->setApartmentNumber($data['address']['apartmentNumber']);
        }
        $address->setPostcode($data['address']['postcode']);
        $address->setCity($data['address']['city']);
        $restaurant->setRestaurantAddress($address);

        $ownerUser = new User();
        $ownerUser->setEmail($data['owner']['email']);
        $ownerUser->setRoles(['ROLE_USER', 'ROLE_OWNER']);
        $hashedPassword = $this->passwordHasher->hashPassword($ownerUser, $data['owner']['password']);
        $ownerUser->setPassword($hashedPassword);
        $owner = new UserData();
        $owner->setPhoneNumber($data['owner']['phoneNumber']);
        $owner->setFirstName($data['owner']['firstName']);
        $owner->setSurname($data['owner']['surname']);
        if (array_key_exists('dateOfBirth', $data['owner']) && !empty($data['owner']['dateOfBirth'])) {
            $dateOfBirth = new DateTimeImmutable($data['owner']['dateOfBirth']);
            $owner->setDateOfBirth($dateOfBirth);
        }
        $owner->setIdUser($ownerUser);
        $mainAddress = new Address();
        $mainAddress->setStreet($data['owner']['mainAddress']['street']);
        $mainAddress->setParcelNumber($data['owner']['mainAddress']['parcelNumber']);
        if (array_key_exists('apartmentNumber', $data['owner']['mainAddress']) && !empty($data['owner']['mainAddress']['apartmentNumber'])) {
            $mainAddress->setApartmentNumber($data['owner']['mainAddress']['apartmentNumber']);
        }
        $mainAddress->setPostcode($data['owner']['mainAddress']['postcode']);
        $mainAddress->setCity($data['owner']['mainAddress']['city']);
        $owner->setMainAddress($mainAddress);
        $restaurant->setOwner($owner);

        $this->getEntityManager()->persist($restaurant);
        try {
            $this->getEntityManager()->flush();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        $restaurantOpeningHours = $this->getEntityManager()->getRepository(OpeningHoursRestaurant::class);

        foreach ($data['openingHoursRestaurant'] as $item) {
            $restaurantOpeningHour = new OpeningHoursRestaurant();
            $restaurantOpeningHour->setRestaurant($restaurant);
            $restaurantOpeningHour->setDayOfWeekFrom($item['daysFrom']);
            $restaurantOpeningHour->setDayOfWeekTo($item['daysTo']);
            $hourFrom = DateTimeImmutable::createFromFormat('H:i', $item['hourFrom']);
            $hourTo = DateTimeImmutable::createFromFormat('H:i', $item['hourTo']);
            $restaurantOpeningHour->setOpenHour($hourFrom);
            $restaurantOpeningHour->setCloseHour($hourTo);
            $result = $restaurantOpeningHours->addOpeningHour($restaurantOpeningHour);
            if (!empty($result)) {
                return array('message'=>'Problem z dodaniem dni i godzin otwarcia restauracji');
            }
        }
//        $categories = $this->getEntityManager()->getRepository(Category::class);
//         odkomentowac pod dodaniu kategori
//        foreach ($data['restaurantCategories'] as $item) {
//            $category = new Category();
//            $category->setName($item['name']);
//            $category->setDescription($item['description']);
//            $result = $categories->addCategory($category);
//            if (empty($result)) {
//                return 'Problem z dodaniem kategori dla restauracji';
//            }
//        }

        return null;
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
