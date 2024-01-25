<?php

namespace App\Controller;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use App\Repository\CourierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private OrderRepository $orderRepository;
    private UserRepository $userRepository;
    private CourierRepository $courierRepository;

    public function __construct(OrderRepository $orderRepository, UserRepository $userRepository, CourierRepository $courierRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
        $this->courierRepository = $courierRepository;
    }

    #[Route('api/findNewOrder', name: 'api_find_new_order', methods: 'GET')]
    public function findNewOrder(): Response
    {
        $token = $this->forward(SecurityController::class . '::decodeToken');
        $content = json_decode($token->getContent(), true);
        $email = $content['username'];
        $user = $this->userRepository->getUserByEmail($email);
        $userTemp = $user[0];
        $userId = $userTemp->getId();
        $courier = $this->courierRepository->getCourierByIdUser($userId);
        $courierId = $courier->getId();
        $tmp = $this->orderRepository->getNewOrderForCourier($courierId);
        if(empty($tmp)) {
            return new Response(
                json_encode(array('message'=>'BRAK')),
                207,
                array('content-type' => 'application/json')
            );
        }
        $order = $tmp;
        return new Response(
            json_encode($order),
            200,
            array('content-type' => 'application/json')
        );
    }
    #[Route('api/findCourierOrder', name: 'api_find_new_order_id', methods: 'GET')]
    public function findCourierOrder(): Response
    {
        $token = $this->forward(SecurityController::class . '::decodeToken');
        $content = json_decode($token->getContent(), true);
        $email = $content['username'];
        $user = $this->userRepository->getUserByEmail($email);
        $userTemp = $user[0];
        $userId = $userTemp->getId();
        $courier = $this->courierRepository->getCourierByIdUser($userId);
        $courierId = $courier->getId();
        $tmp = $this->orderRepository->getOrderForCourier($courierId);
        if(empty($tmp)) {
            return new Response(
                json_encode(array('message'=>'BRAK')),
                207,
                array('content-type' => 'application/json')
            );
        }
        $order = $tmp;
        return new Response(
            json_encode($order),
            200,
            array('content-type' => 'application/json')
        );
    }

    #[Route('api/setCourierAndOrderStatus', name: 'api_set_courier_and_order_status', methods: 'PUT')]
    public function setCourierAndOrderStatus(Request $request): Response
    {
        $paramsTemp = $request->getContent();
        $params = json_decode($paramsTemp, true);
        if (!array_key_exists('order', $params) || empty($params['order'])) {
            return new Response(
                json_encode(array('message'=>'bład')),
                207,
                array('content-type' => 'application/json')
            );
        }
        $token = $this->forward(SecurityController::class . '::decodeToken');
        $content = json_decode($token->getContent(), true);
        $email = $content['username'];
        $user = $this->userRepository->getUserByEmail($email);
        $userTemp = $user[0];
        $userId = $userTemp->getId();
        $courier = $this->courierRepository->getCourierByIdUser($userId);
        $courier->setAssigned(true);
        $order = $this->orderRepository->getOrderById($params['order']);
        $order->setStatus('ACCEPTED');
        $errors = $this->orderRepository->updateOrderStatus($order);
        if ($errors !== null) {
            return new Response(
                json_encode($errors),
                207,
                array('content-type' => 'application/json')
            );
        }
        $errors = $this->courierRepository->updateCourierAssigned($courier);
        if ($errors !== null) {
            return new Response(
                json_encode($errors),
                207,
                array('content-type' => 'application/json')
            );
        }
        return new Response(
            json_encode(array('message'=>'OK')),
            200,
            array('content-type' => 'application/json')
        );
    }

    #[Route('api/admin/getOrders', name: 'api_admin_get_orders', methods: 'GET')]
    public function getOrders(): Response
    {
        $orders = $this->orderRepository->getOrders();
        if(empty($orders)) {
            return new Response(
                json_encode(array('message'=>'Brak kategorii')),
                207,
                array('content-type' => 'application/json')
            );
        }
        return new Response(
            json_encode($orders),
            200,
            array('content-type' => 'application/json')
        );
    }

    #[Route('common/getOrder/{id}', name: 'common_get_order', methods: 'GET')]
    public function getOrder($id): Response
    {
        $order = $this->orderRepository->getOrderById($id);
        if(empty($order)) {
            return new Response(
                json_encode(array('message'=>'Brak')),
                207,
                array('content-type' => 'application/json')
            );
        }
        $restaurant = $order->getRestaurant();
        $restaurantAddress = $restaurant->getRestaurantAddress();
        $fullAddress = $order->getAddress();
        $addressParts = explode(',', $fullAddress, 2);
        $userAddressLine1 = trim($addressParts[0]);
        if (isset($addressParts[1])) {
            $zipcodeCity = explode(' ', trim($addressParts[1]), 2);
            $userAddressZipCode = $zipcodeCity[0];
            $userAddressCity = $zipcodeCity[1] ?? '';
        } else {
            $userAddressZipCode = '';
            $userAddressCity = '';
        }
        $result = [
            'address' => $order->getAddress(),
            'cart' => $order->getCart(),
            'cost' => $order->getCost(),
            'userAddressLine1' => $userAddressLine1,
            'userAddressZipCode' => $userAddressZipCode,
            'userAddressCity' => $userAddressCity,
            'restaurantName'=> $restaurant->getName(),
            'restaurantAddressLine1' => $restaurantAddress->getStreet() . ' ' . $restaurantAddress->getParcelNumber() .
                '/' . $restaurantAddress->getApartmentNumber(),
            'restaurantAddressZipCode' => $restaurantAddress->getPostcode(),
            'restaurantAddressCity' => $restaurantAddress->getCity(),
            'status'=>$order->getStatus(),
            'restaurantId'=>$restaurant->getId(),
            'orderId'=>$order->getId()
        ];
        return new Response(
            json_encode($result),
            200,
            array('content-type' => 'application/json')
        );
    }

    #[Route('api/updateOrderStatus', name: 'api_update_order_status', methods: 'PUT')]
    public function updateOrderStatus(Request $request): Response
    {
        $token = $this->forward(SecurityController::class . '::decodeToken');
        $content = json_decode($token->getContent(), true);
        $email = $content['username'];

        $params = json_decode($request->getContent(), true);
        if (!isset($params['orderId']) || !isset($params['newStatus'])) {
            return new Response(
                json_encode(array('message' => 'Nieprawidłowe dane')),
                207,
                array('content-type' => 'application/json')
            );
        }

        $orderId = $params['orderId'];
        $newStatus = $params['newStatus'];

        $order = $this->orderRepository->getOrderById($orderId);
        if (!$order) {
            return new Response(
                json_encode(array('message' => 'Zamówienie nie znalezione')),
                207,
                array('content-type' => 'application/json')
            );
        }

        $order->setStatus($newStatus);
        $result = $this->orderRepository->addOrder($order);
        if(!empty($result))
        {
            return new Response(
                json_encode(array('message' => 'Niezidentyfikowany błąd')),
                207,
                array('content-type' => 'application/json')
            );
        }
        return new Response(
            json_encode(array('message' => 'Status zamówienia zaktualizowany')),
            200, // OK
            array('content-type' => 'application/json')
        );
    }
}