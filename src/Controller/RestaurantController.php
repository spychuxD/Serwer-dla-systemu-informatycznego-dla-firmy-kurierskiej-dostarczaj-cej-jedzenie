<?php

namespace App\Controller;
use App\Entity\Address;
use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class RestaurantController extends AbstractController
{
    private RestaurantRepository $restaurantRepository;

    public function __construct(RestaurantRepository $restaurantRepository)
    {
        $this->restaurantRepository = $restaurantRepository;
    }

    #[Route('api/public/restaurants', name: 'api_restaurants', methods: 'GET')]
    public function getRestaurants(): JsonResponse
    {
        $restaurants = $this->restaurantRepository->getAllRestaurants();

        if(empty($restaurants)) {
            return new JsonResponse(
                ['message' => 'Nieprzewidziany wyjątek - brak danych. Prosimy o kontakt z serwisem.'],
                207
            );
        }

        return new JsonResponse($restaurants, 200);
    }

    #[Route('api/public/restaurant/{id}', name: 'api_restaurant_id', methods: 'GET')]
    public function getRestaurantInfo($id) :JsonResponse
    {
        $restaurant = $this->restaurantRepository->getOneRestaurantById($id);
        if(empty($restaurant)) {
            return new JsonResponse(
                ['message' => 'Nieprzewidziany wyjątek - brak danych. Prosimy o kontakt z serwisem.'],
                207
            );
        }

        return new JsonResponse($restaurant, 200);
    }

    #[Route('api/public/restaurant/img/{img}', name: 'api_restaurant_img', methods: 'GET')]
    public function getRestaurantImg($img) :Response
    {
        $filename = 'C:/Users/Spychu/PhpstormProjects/WebApp/symfony/app/images/restaurants/' . $img;

        if (file_exists($filename)) {
            $response = new BinaryFileResponse($filename);
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $filename
            );
            $response->headers->set('Content-Disposition', $disposition);
            return $response->deleteFileAfterSend(true);
        } else {
            return new JsonResponse(
                ['message' => 'Nieprzewidziany wyjątek - brak danych. Prosimy o kontakt z serwisem.'],
                207
            );
        }
    }
}