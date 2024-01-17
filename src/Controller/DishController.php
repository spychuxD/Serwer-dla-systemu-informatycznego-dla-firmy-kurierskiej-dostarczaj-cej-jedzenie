<?php

namespace App\Controller;
use App\Repository\DishCategoryRepository;
use App\Repository\DishIngridientRepository;
use App\Repository\DishRepository;
use App\Repository\IngridientCategoryRepository;
use App\Repository\IngridientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DishController extends AbstractController
{
    private DishRepository $dishRepository;
    private DishIngridientRepository $dishIngridientRepository;
    private IngridientCategoryRepository $ingridientCategoryRepository;
    private DishCategoryRepository $dishCategoryRepository;
    private IngridientRepository $ingridientRepository;

    public function __construct(DishRepository $dishRepository, DishIngridientRepository $dishIngridientRepository,
                                IngridientCategoryRepository $ingridientCategoryRepository, DishCategoryRepository $dishCategoryRepository, IngridientRepository $ingridientRepository)
    {
        $this->dishRepository = $dishRepository;
        $this->dishIngridientRepository = $dishIngridientRepository;
        $this->ingridientCategoryRepository = $ingridientCategoryRepository;
        $this->dishCategoryRepository = $dishCategoryRepository;
        $this->ingridientRepository = $ingridientRepository;
    }

    #[Route('common/dishesByRestaurant/{id}', name: 'common_dishes_by_restaurant_id', methods: 'GET')]
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

    #[Route('common/dishingridients/{id}', name: 'common_dish_ingridients_by_id', methods: 'GET')]
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

    #[Route('api/admin/getDishes', name: 'api_admin_get_dishes', methods: 'GET')]
    public function getDishes(): Response
    {
        $dishes = $this->dishRepository->getDishes();
        if(empty($dishes)) {
            return new Response(
                json_encode(array('message'=>'Brak kategorii')),
                207,
                array('content-type' => 'application/json')
            );
        }
        return new Response(
            json_encode($dishes),
            200,
            array('content-type' => 'application/json')
        );
    }

    #[Route('api/admin/addDishCategory', name: 'api_add_dish_category', methods: 'POST')]
    public function addDishCategory(Request $request): Response
    {
        $content = $request->getContent();
        $category = json_decode($content, true);
        $result = $this->dishCategoryRepository->setCategory($category['category']);
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

    #[Route('api/admin/addIngridientCategory', name: 'api_add_ingridient_category', methods: 'POST')]
    public function addIngridientCategory(Request $request): Response
    {
        $content = $request->getContent();
        $category = json_decode($content, true);
        $result = $this->ingridientCategoryRepository->setCategory($category['category']);
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

    #[Route('api/admin/getDishesCategories', name: 'api_admin_get_dishes_categories', methods: 'GET')]
    public function getDishesCategories(): Response
    {
        $dishes = $this->dishCategoryRepository->getCategories();
        if(empty($dishes)) {
            return new Response(
                json_encode(array('message'=>'Brak kategorii')),
                207,
                array('content-type' => 'application/json')
            );
        }
        return new Response(
            json_encode($dishes),
            200,
            array('content-type' => 'application/json')
        );
    }

    #[Route('api/admin/getIngridientsCategories', name: 'api_admin_get_ingridients_categories', methods: 'GET')]
    public function getIngridientsCategories(): Response
    {
        $dishes = $this->ingridientCategoryRepository->getCategories();
        if(empty($dishes)) {
            return new Response(
                json_encode(array('message'=>'Brak kategorii')),
                207,
                array('content-type' => 'application/json')
            );
        }
        return new Response(
            json_encode($dishes),
            200,
            array('content-type' => 'application/json')
        );
    }

    #[Route('api/admin/addDish', name: 'api_admin_add_dish', methods: 'POST')]
    public function addDish(Request $request): Response
    {
        $content = $request->getContent();
        $dish = json_decode($content, true);
        $result = $this->dishRepository->setDish($dish['dish']);
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

    #[Route('api/admin/getCateogryIngridients/{id}', name: 'api_admin_get_ingridients_category_id', methods: 'GET')]
    public function getCateogryIngridients($id): Response
    {
        $dishes = $this->ingridientRepository->getCategoryIngridients($id);
        if(empty($dishes)) {
            return new Response(
                json_encode(array('message'=>'Brak kategorii')),
                207,
                array('content-type' => 'application/json')
            );
        }
        return new Response(
            json_encode($dishes),
            200,
            array('content-type' => 'application/json')
        );
    }
}