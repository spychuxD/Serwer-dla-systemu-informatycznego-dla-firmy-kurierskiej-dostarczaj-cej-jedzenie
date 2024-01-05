<?php

namespace App\Controller;
use App\Repository\CourierRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CourierController extends AbstractController
{
    private CourierRepository $courierRepository;
    private UserRepository $userRepository;

    public function __construct(CourierRepository $courierRepository, UserRepository $userRepository)
    {
        $this->courierRepository = $courierRepository;
        $this->userRepository = $userRepository;
    }

    #[Route('api/getCourierStatus', name: 'api_get_courier_status', methods: 'GET')]
    public function findOrder(): Response
    {
        $token = $this->forward(SecurityController::class . '::decodeToken');
        $content = json_decode($token->getContent(), true);
        $email = $content['username'];
        $user = $this->userRepository->getUserByEmail($email);
        $userTemp = $user[0];
        $userId = $userTemp->getId();
        $courier = $this->courierRepository->getCourierByIdUser($userId);
        $courierAssigned = $courier->isAssigned();
        if($courierAssigned === true) {
            return new Response(
                json_encode(array('status'=>'ZAJĘTY')),
                207,
                array('content-type' => 'application/json')
            );
        } else {
            return new Response(
                json_encode(array('status'=>'WOLNY')),
                207,
                array('content-type' => 'application/json')
            );
        }
    }
}