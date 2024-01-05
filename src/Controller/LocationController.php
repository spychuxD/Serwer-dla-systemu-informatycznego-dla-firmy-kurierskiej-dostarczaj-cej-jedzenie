<?php

namespace App\Controller;
use App\Entity\Courier;
use App\Repository\CourierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
class LocationController extends AbstractController
{
//    private DishRepository $dishRepository;
//    private DishIngridientRepository $dishIngridientRepository;
//
//    public function __construct(DishRepository $dishRepository, DishIngridientRepository $dishIngridientRepository)
//    {
//        $this->dishRepository = $dishRepository;
//        $this->dishIngridientRepository = $dishIngridientRepository;
//    }
    private CourierRepository $courierRepository;

    public function __construct(CourierRepository $courierRepository)
    {
        $this->courierRepository = $courierRepository;
    }

    #[Route('api/setLocation/{id}', name: 'common_set_location_id', methods: 'PUT')]
    public function setLocation(Request $request, $id, EntityManagerInterface $entityManager): Response
    {
        $content = $request->getContent();
        $location = json_decode($content, true);
        $tmp = $this->courierRepository->getCourierById($id);
        $courier = $tmp[0];
        $courier->setLat($location['lat']);
        $courier->setLng($location['lng']);
        $entityManager->persist($courier);
        try {
            $entityManager->flush();
        } catch (Exception $e) {
            return new Response(
                json_encode($e->getMessage()),
                207,
                array('content-type' => 'application/json')
            );
        }
        return new Response(
            json_encode(array('message'=>'OK')),
            200,
            array('content-type' => 'application/json')
        );
    }

    #[Route('common/getLocation/{id}', name: 'common_get_location_id', methods: 'GET')]
    public function getLocation($id): Response
    {
        $courier = $this->courierRepository->getCourierById($id);
        $lat = $courier->getLat();
        $lng = $courier->getLng();
        $response = [
            'lat'=>$lat,
            'lng'=>$lng
        ];
        return new Response(
            json_encode($response),
            200,
            array('content-type' => 'application/json')
        );
    }
}