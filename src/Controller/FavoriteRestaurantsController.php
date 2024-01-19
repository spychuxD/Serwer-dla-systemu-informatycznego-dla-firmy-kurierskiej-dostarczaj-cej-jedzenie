<?php

namespace App\Controller;

use App\Repository\FavoriteRestaurantsRepository;
use App\Repository\RestaurantRepository;
use App\Repository\UserDataRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FavoriteRestaurantsController extends AbstractController
{
    private FavoriteRestaurantsRepository $favoriteRestaurantsRepository;
    private UserRepository $userRepository;
    private UserDataRepository $userDataRepository;
    private RestaurantRepository $restaurantRepository;

    public function __construct(FavoriteRestaurantsRepository $favoriteRestaurantsRepository, UserRepository $userRepository,
                                UserDataRepository $userDataRepository, RestaurantRepository $restaurantRepository)
    {
        $this->favoriteRestaurantsRepository = $favoriteRestaurantsRepository;
        $this->userRepository = $userRepository;
        $this->userDataRepository = $userDataRepository;
        $this->restaurantRepository = $restaurantRepository;
    }

    #[Route('api/addToFavorite/{id}', name: 'api_add_to_favorite', methods: 'POST')]
    public function addToFavorite($id) : Response
    {
        $response = $this->forward(SecurityController::class . '::decodeToken');
        $data = json_decode($response->getContent(), true);
        $email = $data['username'];
        $user = $this->userRepository->getUserByEmail($email);
        $userTemp = $user[0];
        $userId = $userTemp->getId();
        $userData = $this->userDataRepository->getUserDataByUserId($userId);
        $restaurant = $this->restaurantRepository->getRestaurantById($id);
        $result = $this->favoriteRestaurantsRepository->addToFavorite($restaurant, $userData);
        if(!empty($result)) {
            return new Response(
                json_encode($result),
                207,
                array('content-type' => 'application/json')
            );
        }
        return new Response(
            json_encode(array('message'=>'Restauracja została dodana do ulubionych')),
            201,
            array('content-type' => 'application/json')
        );
    }
    #[Route('api/favoriteRestaurants', name: 'api_favorite_restaurants', methods: 'GET')]
    public function getFavoriteRestaurants() :Response
    {
        $response = $this->forward(SecurityController::class . '::decodeToken');
        $data = json_decode($response->getContent(), true);
        $email = $data['username'];
        $user = $this->userRepository->getUserByEmail($email);
        $userTemp = $user[0];
        $userId = $userTemp->getId();
        $restaurants = $this->favoriteRestaurantsRepository->getFavoriteRestaurantsByUserId($userId);
        if(empty($restaurants)) {
            return new Response(
                json_encode(array('Brak ulubionych restauracji')),
                207,
                array('content-type' => 'application/json')
            );
        }
        return new Response(
            json_encode($restaurants),
            200,
            array('content-type' => 'application/json')
        );
    }

    #[Route('api/removeFromFavorite/{id}', name: 'api_remove_from_favorite', methods: 'DELETE')]
    public function removeFromFavorite($id) : Response
    {
        $response = $this->forward(SecurityController::class . '::decodeToken');
        $data = json_decode($response->getContent(), true);
        $email = $data['username'];
        $user = $this->userRepository->getUserByEmail($email);
        $userTemp = $user[0];
        $userId = $userTemp->getId();
        $userData = $this->userDataRepository->getUserDataByUserId($userId);

        $restaurant = $this->restaurantRepository->getRestaurantById($id);
        $result = $this->favoriteRestaurantsRepository->removeFromFavorite($restaurant, $userData);

        if($result) {
            return new Response(
                json_encode(array('message' => 'Restauracja została usunięta z ulubionych')),
                200,
                array('content-type' => 'application/json')
            );
        } else {
            return new Response(
                json_encode(array('error' => 'Nie udało się usunąć restauracji z ulubionych')),
                207,
                array('content-type' => 'application/json')
            );
        }
    }
}
