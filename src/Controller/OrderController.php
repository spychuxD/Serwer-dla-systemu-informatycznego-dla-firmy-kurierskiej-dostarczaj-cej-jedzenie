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
        $order = $tmp[0];
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
        $order = $tmp[0];
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
}