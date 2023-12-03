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
                json_encode(array('Nieprzewidziany błąd')),
                207,
                array('content-type' => 'application/json')
            );
        }

        $restaurantCategories = [];
        foreach ($dishes as $dish) {
            $categoryName = $dish['categoryName'];
            $categoryDescription = $dish['categoryDescription'];
            $categoryId = $dish['categoryId'];

            if (!array_key_exists($categoryName, $restaurantCategories)) {
                $restaurantCategories[$categoryName]['categoryId'] = $categoryId;
                $restaurantCategories[$categoryName]['categoryName'] = $categoryName;
                $restaurantCategories[$categoryName]['categoryDescription'] = $categoryDescription;
                $restaurantCategories[$categoryName]['categoryDishes'] = [];
            }

            $restaurantCategories[$categoryName]['categoryDishes'][] = [
                'dishId' => $dish['dishId'],
                'dishName' => $dish['dishName'],
                'dishDescription' => $dish['dishDescription'],
                'dishCategoryId' => $categoryId,
                'dishcategoryName' => $categoryName,
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

        if (empty($ingridients)) {
            return new Response(
                json_encode(array('Nieprzewidziany błąd')),
                207,
                array('content-type' => 'application/json')
            );
        }

        $ingridientCategories = [];

        foreach ($ingridients as $ingridient) {
            $categoryId = $ingridient['ingridientCategoryId'];
            $categoryName = $ingridient['ingridientCategoryName'];
            $categoryDescription = $ingridient['ingridientCategoryDescription'];
            $isMultiOption = $ingridient['isMultiOption'];

            $ingridientTemp = [
                'ingridientId' => $ingridient['ingridientId'],
                'ingridientName' => $ingridient['ingridientName'],
                'ingridientDescription' => $ingridient['ingridientDescription'],
                'price' => $ingridient['price'],
            ];

            $categoryFound = false;

            foreach ($ingridientCategories as &$ingridientCategory) {
                if ($ingridientCategory['ingridientCategoryName'] === $categoryName) {
                    $existingInCategory = false;
                    foreach ($ingridientCategory['ingridients'] as $existingIngridient) {
                        if ($existingIngridient['ingridientId'] === $ingridient['ingridientId']) {
                            $existingInCategory = true;
                            break;
                        }
                    }

                    if (!$existingInCategory) {
                        $ingridientCategory['ingridients'][] = $ingridientTemp;
                    }

                    $categoryFound = true;
                    break;
                }
            }

            if (!$categoryFound) {
                $ingridientCategories[] = [
                    'ingridientCategoryId' => $categoryId,
                    'ingridientCategoryName' => $categoryName,
                    'ingridientCategoryDescription' => $categoryDescription,
                    'isMultiOption' => $isMultiOption,
                    'ingridients' => [$ingridientTemp],
                ];
            }
        }

        return new Response(
            json_encode($ingridientCategories),
            200,
            array('content-type' => 'application/json')
        );
    }
}