<?php

namespace App\Controller;
use App\Entity\Address;
use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class RestaurantController extends AbstractController
{
    private RestaurantRepository $restaurantRepository;
    private CategoryRepository $categoryRepository;

    public function __construct(RestaurantRepository $restaurantRepository, CategoryRepository $categoryRepository)
    {
        $this->restaurantRepository = $restaurantRepository;
        $this->categoryRepository = $categoryRepository;
    }

    #[Route('common/restaurants', name: 'common_restaurants', methods: 'GET')]
    public function getRestaurants(): JsonResponse
    {
        $restaurants = $this->restaurantRepository->getAllRestaurants();

        if(empty($restaurants)) {
            return new JsonResponse(
                ['message' => 'Nieprzewidziany błąd'],
                207
            );
        }

        return new JsonResponse($restaurants, 200);
    }

    #[Route('common/restaurant/{id}', name: 'common_restaurant_id', methods: 'GET')]
    public function getRestaurantInfo($id) :JsonResponse
    {
        $restaurant = $this->restaurantRepository->getOneRestaurantById($id);
        if(empty($restaurant)) {
            return new JsonResponse(
                ['message' => 'Nieprzewidziany błąd'],
                207
            );
        }

        return new JsonResponse($restaurant, 200);
    }

    #[Route('api/admin/restaurant/{id}', name: 'api_restaurant_id', methods: 'GET')]
    public function getPrivateRestaurantInfo($id) :JsonResponse
    {
        //sprawdzenie roli
        $restaurant = $this->restaurantRepository->getOneRestaurantByIdToEdit($id);

        if(empty($restaurant)) {
            return new JsonResponse(
                ['message' => 'Nieprzewidziany błąd'],
                207
            );
        }

        return new JsonResponse($restaurant, 200);
    }

    #[Route('common/restaurant/img/{img}', name: 'common_restaurant_img', methods: 'GET')]
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
                ['message' => 'Nieprzewidziany błąd'],
                207
            );
        }
    }

    #[Route('api/admin/addRestaurant', name: 'api_add_restaurant', methods: 'POST')]
    public function addRestaurant(Request $request): Response
    {
        $content = $request->getContent();
        $restaurant = json_decode($content, true);
        $result = $this->restaurantRepository->setRestaurant($restaurant['restaurant']);
        if(!empty($result)) {
            return new Response(
                json_encode($result),
                207,
                array('content-type' => 'application/json')
            );
        }
        return new Response(
            json_encode(array('message'=>'Pomyślnie dodano restauracje do systemu')),
            201,
            array('content-type' => 'application/json')
        );
    }

    #[Route('api/admin/addCategory', name: 'api_add_category', methods: 'POST')]
    public function addCategory(Request $request): Response
    {
        $content = $request->getContent();
        $category = json_decode($content, true);
        $result = $this->restaurantRepository->setCategory($category['category']);
        if(!empty($result)) {
            return new Response(
                json_encode($result),
                207,
                array('content-type' => 'application/json')
            );
        }
        return new Response(
            json_encode(array('message'=>'Pomyślnie dodano kategorię do systemu')),
            201,
            array('content-type' => 'application/json')
        );
    }

    #[Route('api/admin/getCategories', name: 'api_admin_get_categories', methods: 'GET')]
    public function getCategories(): Response
    {
        $categories = $this->categoryRepository->getCategories();
        if(empty($categories)) {
            return new Response(
                json_encode(array('message'=>'Brak kategorii')),
                207,
                array('content-type' => 'application/json')
            );
        }
        return new Response(
            json_encode($categories),
            200,
            array('content-type' => 'application/json')
        );
    }
}