<?php

namespace App\Controller;

use App\Entity\RestaurantRatings;
use App\Repository\RestaurantRatingsRepository;
use App\Repository\RestaurantRepository;
use App\Repository\UserDataRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RestaurantRatingsController extends AbstractController
{
    private RestaurantRatingsRepository $restaurantRatingsRepository;
    private UserRepository $userRepository;
    private UserDataRepository $userDataRepository;
    private RestaurantRepository $restaurantRepository;

    public function __construct(RestaurantRatingsRepository $restaurantRatingsRepository, UserRepository $userRepository,
                                UserDataRepository $userDataRepository, RestaurantRepository $restaurantRepository)
    {
        $this->restaurantRatingsRepository = $restaurantRatingsRepository;
        $this->userRepository = $userRepository;
        $this->userDataRepository = $userDataRepository;
        $this->restaurantRepository = $restaurantRepository;
    }

    #[Route('api/ratings/add/{id}', name: 'api_add_rating', methods: 'POST')]
    public function addRating(Request $request, $id): Response
    {
        $response = $this->forward(SecurityController::class . '::decodeToken');
        $data = json_decode($response->getContent(), true);
        $email = $data['username'];
        $user = $this->userRepository->getUserByEmail($email);
        $userTemp = $user[0];
        $userId = $userTemp->getId();
        $userData = $this->userDataRepository->getUserDataByUserId($userId);
        $restaurant = $this->restaurantRepository->getRestaurantById($id);
        $ratingData = json_decode($request->getContent(), true);
        // Tworzenie i ustawianie właściwości nowej oceny
        $rating = new RestaurantRatings();
//        $rating->setValue($ratingData['value']);
        $rating->setValue(4.5);
//        $rating->setDescription($ratingData['description']);
        $rating->setDescription('test');
        $rating->setRestaurant($restaurant);
        $rating->setUserData($userData);
        $now = new DateTime();
//        $currentDateTime = $now->format('Y-m-d H:i:s');
        $rating->setDate($now);

        $result = $this->restaurantRatingsRepository->addRating($rating);
        if(empty($result)) {
            return new Response(
                json_encode(['message' => 'Ocena dodana pomyślnie']),
                201,
                array('content-type' => 'application/json')
            );
        }
        return new Response(
            json_encode(['message'=>'Dodano opinię']),
            200,
            array('content-type' => 'application/json')
        );
    }

    #[Route('api/ratings/remove/{id}', name: 'api_remove_rating', methods: 'DELETE')]
    public function removeRating($id): Response
    {
        $rating = $this->restaurantRatingsRepository->getRatingById($id);

        if ($rating) {
            $this->restaurantRatingsRepository->removeRating($rating);
            return new Response(
                json_encode(['message' => 'Opinia usunięta pomyślnie']),
                200,
                array('content-type' => 'application/json')
            );
        }

        return new Response(
            json_encode(['message' => 'Opinia nie znaleziona']),
            207,
            array('content-type' => 'application/json')
        );
    }

    #[Route('api/userRatings', name: 'api_get_rating', methods: 'GET')]
    public function getRating(): Response
    {
        $response = $this->forward(SecurityController::class . '::decodeToken');
        $data = json_decode($response->getContent(), true);
        $email = $data['username'];
        $user = $this->userRepository->getUserByEmail($email);
        $userTemp = $user[0];
        $userId = $userTemp->getId();
        $rating = $this->restaurantRatingsRepository->getUserRatings($userId);

        if (!empty($rating)) {
            return new Response(
                json_encode($rating),
                200,
                array('content-type' => 'application/json')
            );
        }

        return new Response(
            json_encode(['message' => 'Brak opinii użytkownika']),
            207,
            array('content-type' => 'application/json')
        );
    }

    #[Route('api/ratings', name: 'api_get_all_ratings', methods: 'GET')]
    public function getAllRatings(): Response
    {
        $ratings = $this->restaurantRatingsRepository->getAllRatings();

        return new Response(
            json_encode($ratings),
            200,
            array('content-type' => 'application/json')
        );
    }
}
