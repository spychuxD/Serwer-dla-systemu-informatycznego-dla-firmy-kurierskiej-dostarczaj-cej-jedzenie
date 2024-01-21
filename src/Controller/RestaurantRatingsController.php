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

    #[Route('common/addRating/{id}', name: 'api_add_rating', methods: 'POST')]
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
        $rating->setValue($ratingData['value']);
//        $rating->setValue(4.5);
        $rating->setDescription($ratingData['description']);
//        $rating->setDescription('test');
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

    #[Route('common/ratings/remove/{id}', name: 'api_remove_rating', methods: 'DELETE')]
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

    #[Route('api/userRatings', name: 'api_user_ratings', methods: 'GET')]
    public function getRating(): Response
    {
        $response = $this->forward(SecurityController::class . '::decodeToken');
        $data = json_decode($response->getContent(), true);
        $email = $data['username'];
        $user = $this->userRepository->getUserByEmail($email);
        $userTemp = $user[0];
        $userId = $userTemp->getId();
        $ratings = $this->restaurantRatingsRepository->getUserRatings($userId);

        if(!empty($ratings)) {
            $result = [];
            foreach ($ratings as $rating) {
                $restaurant = $rating->getRestaurant();
                $userData = $rating->getUserData();
                $user = $userData->getIdUser();
                $result[] = [
                    'restaurantName'=>$restaurant->getName(),
                    'restaurantId'=>$restaurant->getId(),
                    'date'=>$rating->getDate()->format('Y-m-d H:i:s'),
                    'userEmail'=>$user->getEmail(),
                    'value'=>$rating->getValue(),
                    'description'=>$rating->getDescription()
                ];
            }
            if(!empty($result)) {
                return new Response(
                    json_encode($result),
                    200,
                    array('content-type' => 'application/json')
                );
            } else {
                return new Response(
                    json_encode(['message'=>'Wystąpił nieoczekiwany błąd']),
                    207,
                    array('content-type' => 'application/json')
                );
            }
        }
        return new Response(
            json_encode($ratings),
            200,
            array('content-type' => 'application/json')
        );
    }
#[Route('common/restaurantRatings/{id}', name: 'common_restaurant_ratings', methods: 'GET')]
    public function getRestaurantRating($id): Response
    {
        $ratings = $this->restaurantRatingsRepository->getRestaurantRatings($id);

        if(!empty($ratings)) {
            $result = [];
            foreach ($ratings as $rating) {
                $restaurant = $rating->getRestaurant();
                $userData = $rating->getUserData();
                $user = $userData->getIdUser();
                $result[] = [
                    'restaurantName'=>$restaurant->getName(),
                    'restaurantId'=>$restaurant->getId(),
                    'date'=>$rating->getDate()->format('Y-m-d H:i:s'),
                    'userEmail'=>$user->getEmail(),
                    'value'=>$rating->getValue(),
                    'description'=>$rating->getDescription()
                ];
            }
            if(!empty($result)) {
                return new Response(
                    json_encode($result),
                    200,
                    array('content-type' => 'application/json')
                );
            } else {
                return new Response(
                    json_encode(['message'=>'Wystąpił nieoczekiwany błąd']),
                    207,
                    array('content-type' => 'application/json')
                );
            }
        }
        return new Response(
            json_encode($ratings),
            200,
            array('content-type' => 'application/json')
        );
    }

    #[Route('common/ratings', name: 'api_get_all_ratings', methods: 'GET')]
    public function getAllRatings(): Response
    {
        $ratings = $this->restaurantRatingsRepository->getAllRatings();
        if(!empty($ratings)) {
            $result = [];
            foreach ($ratings as $rating) {
                $restaurant = $rating->getRestaurant();
                $userData = $rating->getUserData();
                $user = $userData->getIdUser();
                $result[] = [
                    'restaurantName'=>$restaurant->getName(),
                    'restaurantId'=>$restaurant->getId(),
                    'date'=>$rating->getDate()->format('Y-m-d H:i:s'),
                    'userEmail'=>$user->getEmail(),
                    'value'=>$rating->getValue(),
                    'description'=>$rating->getDescription()
                ];
            }
            if(!empty($result)) {
                return new Response(
                    json_encode($result),
                    200,
                    array('content-type' => 'application/json')
                );
            } else {
                return new Response(
                    json_encode(['message'=>'Wystąpił nieoczekiwany błąd']),
                    207,
                    array('content-type' => 'application/json')
                );
            }
        }
        return new Response(
            json_encode($ratings),
            200,
            array('content-type' => 'application/json')
        );
    }
}
