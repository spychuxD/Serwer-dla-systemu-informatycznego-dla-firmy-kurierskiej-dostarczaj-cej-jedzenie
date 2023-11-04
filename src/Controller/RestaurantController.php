<?php

namespace App\Controller;
use App\Entity\Address;
use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use Doctrine\DBAL\Driver\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class RestaurantController extends AbstractController
{
    private RestaurantRepository $restaurantRepository;

    public function __construct(RestaurantRepository $restaurantRepository)
    {
        $this->restaurantRepository = $restaurantRepository;
    }

    /**
     * @Route("/api/restaurants", methods={"GET"})
     */
    public function getRestaurants()
    {
        $restaurants = $this->restaurantRepository->getAll();
        if(empty($restaurants)) {
            return new Response(
                json_encode(array('Nieprzewidziany wyjątek - brak danych. Prosimy o kontakt z serwisem.')),
                204,
                array('content-type' => 'application/json')
            );
        }

        return new Response(
            json_encode($restaurants),
            200,
            array('content-type' => 'application/json')
        );
    }

    /**
     * @Route("/restaurant/{id}", methods={"GET"})
     */
    public function getRestaurantInfo($id)
    {
        $restaurant = $this->restaurantRepository->getOneById($id);
        if(empty($restaurant)) {
            return new Response(
                json_encode(array('Nieprzewidziany wyjątek - brak danych. Prosimy o kontakt z serwisem.')),
                204,
                array('content-type' => 'application/json')
            );
        }

        return new Response(
            json_encode($restaurant),
            200,
            array('content-type' => 'application/json')
        );
    }
}