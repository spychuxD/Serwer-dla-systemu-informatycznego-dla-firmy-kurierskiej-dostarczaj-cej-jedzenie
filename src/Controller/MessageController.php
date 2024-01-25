<?php

namespace App\Controller;

use App\Repository\MessagesRepository;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    private MessagesRepository $messagesRepository;
    private OrderRepository $orderRepository;

    public function __construct(MessagesRepository $messagesRepository, OrderRepository $orderRepository)
    {
        $this->messagesRepository = $messagesRepository;
        $this->orderRepository = $orderRepository;
    }

    #[Route('common/addMessage/{id}', name: 'common_add_message_order_id', methods: 'POST')]
    public function addMessage(Request $request, $id) : Response
    {
        $jsonString = $request->getContent();
        $params = json_decode($jsonString, true);
        if(!array_key_exists('message', $params) || empty($params['message'])) {
            return new Response(
                json_encode(array('message'=>'Nie można wysłać pustej wiadomości')),
                207,
                array('content-type' => 'application/json')
            );
        }
        $order = $this->orderRepository->getOrderById($id);
        $result = $this->messagesRepository->addMessage($params['message'], $params['sender'], $order, $params['date']);
        if(!empty($result)) {
            return new Response(
                json_encode($result),
                207,
                array('content-type' => 'application/json')
            );
        }
        return new Response(
            json_encode(array('message'=>'Widomość została wysłana')),
            201,
            array('content-type' => 'application/json')
        );
    }
    #[Route('common/getMessages/{id}', name: 'common_get_messages_order_id', methods: 'GET')]
    public function getMessages($id) :Response
    {
        $messages = $this->messagesRepository->getMessagesByOrderId($id);
        if(empty($messages)) {
            return new Response(
                json_encode(array('Brak')),
                200,
                array('content-type' => 'application/json')
            );
        }
        $result = [];
        foreach ($messages as $message) {
            $date = $message->getDate()->format('Y-m-d H:i:s');
            $order = $message->getOrderId();
            $result[] = [
                'sender'=>$message->getSender(),
                'date'=>$date,
                'orderId'=>$order->getId(),
                'message'=>$message->getMessage()
            ];
        }
        return new Response(
            json_encode($result),
            200,
            array('content-type' => 'application/json')
        );
    }
}
