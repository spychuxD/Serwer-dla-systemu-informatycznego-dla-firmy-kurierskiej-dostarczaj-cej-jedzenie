<?php

namespace App\Controller;

use App\Repository\FavoriteRestaurantsRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavoriteRestaurantsController extends AbstractController
{
    private FavoriteRestaurantsRepository $favoriteRestaurantsRepository;
    private UserRepository $userRepository;

    public function __construct(FavoriteRestaurantsRepository $favoriteRestaurantsRepository, UserRepository $userRepository)
    {
        $this->favoriteRestaurantsRepository = $favoriteRestaurantsRepository;
        $this->userRepository = $userRepository;
    }
    #[Route('user/favoriteRestaurants', name: 'common_user_favorite_restaurants')]
    public function getFavoriteRestaurants()
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
}
