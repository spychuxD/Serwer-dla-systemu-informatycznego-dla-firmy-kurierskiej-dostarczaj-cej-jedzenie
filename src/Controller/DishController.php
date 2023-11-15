<?php

namespace App\Controller;
use App\Repository\DishIngridientRepository;
use App\Repository\DishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DishController extends AbstractController
{
    private DishRepository $dishRepository;
    private DishIngridientRepository $dishIngridientRepository;

    public function __construct(DishRepository $dishRepository, DishIngridientRepository $dishIngridientRepository)
    {
        $this->dishRepository = $dishRepository;
        $this->dishIngridientRepository = $dishIngridientRepository;
    }

    #[Route('api/public/dishesByRestaurant/{id}', name: 'api_dishes_by_restaurant_id', methods: 'GET')]
    public function getRestaurantDishes($id)
    {
        $dishes = $this->dishRepository->getByIdResturant($id);
        if(empty($dishes)) {
            return new Response(
                json_encode(array('Nieprzewidziany wyjątek - brak danych. Prosimy o kontakt z serwisem.')),
                207,
                array('content-type' => 'application/json')
            );
        }

        $restaurantCategories = [];
        foreach ($dishes as $dish) {
            $categoryName = $dish['categoryName'];
            $categoryDescription = $dish['categoryDescription'];

            if (!array_key_exists($categoryName, $restaurantCategories)) {
                $restaurantCategories[$categoryName]['categoryName'] = $categoryName;
                $restaurantCategories[$categoryName]['categoryDescription'] = $categoryDescription;
                $restaurantCategories[$categoryName]['categoryDishes'] = [];
            }

            $restaurantCategories[$categoryName]['categoryDishes'][] = [
                'dishId' => $dish['dishId'],
                'dishName' => $dish['dishName'],
                'dishDescription' => $dish['dishDescription'],
                'price' => $dish['price'],
            ];
        }
        return new Response(
            json_encode($restaurantCategories),
            200,
            array('content-type' => 'application/json')
        );
    }

    #[Route('api/public/dishingridients/dish/{id}', name: 'api_dishingridients_by_id', methods: 'GET')]
    public function getDishIngridients($id)
    {
        $ingridients = $this->dishIngridientRepository->getAllByDishId($id);
        if(empty($ingridients)) {
            return new Response(
                json_encode(array('Nieprzewidziany wyjątek - brak danych. Prosimy o kontakt z serwisem.')),
                207,
                array('content-type' => 'application/json')
            );
        }

        $ingridientCategories = [];
        foreach ($ingridients as $ingridient) {
            $categoryName = $ingridient['ingridientCategoryName'];
            $categoryDescription = $ingridient['ingridientCategoryDescription'];
            $isMultiOption = $ingridient['isMultiOption'];

            if (!array_key_exists($categoryName, $ingridientCategories)) {
                $ingridientCategories[$categoryName]['name'] = $categoryName;
                $ingridientCategories[$categoryName]['description'] = $categoryDescription;
                $ingridientCategories[$categoryName]['isMultiOption'] = $isMultiOption;
                $ingridientCategories[$categoryName]['ingridients'] = [];
            }

            $ingridientCategories[$categoryName]['ingridients'][] = [
                'ingridientId' => $ingridient['ingridientId'],
                'ingridientName' => $ingridient['ingridientName'],
                'ingridientDescription' => $ingridient['ingridientDescription'],
                'price' => $ingridient['price'],
            ];
        }
        return new Response(
            json_encode($ingridientCategories),
            200,
            array('content-type' => 'application/json')
        );
    }
}