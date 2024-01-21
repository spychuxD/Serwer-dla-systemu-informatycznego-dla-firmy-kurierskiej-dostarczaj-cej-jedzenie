<?php

namespace App\Controller;
use App\Entity\CourierWorkDays;
use App\Repository\CourierRepository;
use App\Repository\CourierWorkDaysRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CourierController extends AbstractController
{
    private CourierRepository $courierRepository;
    private UserRepository $userRepository;
    private CourierWorkDaysRepository $courierWorkDaysRepository;

    public function __construct(CourierRepository $courierRepository, UserRepository $userRepository, CourierWorkDaysRepository $courierWorkDaysRepository)
    {
        $this->courierRepository = $courierRepository;
        $this->userRepository = $userRepository;
        $this->courierWorkDaysRepository = $courierWorkDaysRepository;
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
                200,
                array('content-type' => 'application/json')
            );
        } else {
            return new Response(
                json_encode(array('status'=>'WOLNY')),
                200,
                array('content-type' => 'application/json')
            );
        }
    }

    #[Route('api/getWorkDays', name: 'api_get_courier_work_days', methods: 'GET')]
    public function getCourierWorkDays(): Response
    {
        $token = $this->forward(SecurityController::class . '::decodeToken');
        $content = json_decode($token->getContent(), true);
        $email = $content['username'];
        $user = $this->userRepository->getUserByEmail($email);
        $userTemp = $user[0];
        $userId = $userTemp->getId();
        $workDays = $this->courierWorkDaysRepository->getCourierWorkDays($userId);
        if(empty($workDays)) {
            return new Response(
                json_encode(array('message'=>'Brak zapisanych godziny pracy. Wyjątek')),
                207,
                array('content-type' => 'application/json')
            );
        } else {
            $result = [];
            foreach ($workDays as $workDay) {
                $result[] = [
                    'date'=>$workDay->getDay(),
                    'start'=>$workDay->getHourFrom(),
                    'stop'=>$workDay->getHourTo()
                ];
            }
            return new Response(
                json_encode($result),
                200,
                array('content-type' => 'application/json')
            );
        }
    }

    #[Route('api/addWorkDay', name: 'api_add_work_day', methods: 'POST')]
    public function addWorkDay(Request $request): Response
    {
        $token = $this->forward(SecurityController::class . '::decodeToken');
        $content = json_decode($token->getContent(), true);
        $email = $content['username'];
        $user = $this->userRepository->getUserByEmail($email);
        $userTemp = $user[0];
        $userId = $userTemp->getId();
        $courier = $this->courierRepository->getCourierByIdUser($userId);
        $content = $request->getContent();
        $params = json_decode($content, true);
        $courierWorkDay = new CourierWorkDays();
        $courierWorkDay->setCourier($courier);
        $courierWorkDay->setDay($params['date']);
        $courierWorkDay->setHourFrom($params['hourFrom']);
        $courierWorkDay->setHourTo($params['hourTo']);
        $result = $this->courierWorkDaysRepository->addWorkDay($courierWorkDay);
        if(empty($result)) {
            return new Response(
                json_encode(array('message'=>'Dodano')),
                201,
                array('content-type' => 'application/json')
            );
        } else {
            return new Response(
                json_encode(array('message'=>'Wystąpił błąd przy dodawaniu')),
                207,
                array('content-type' => 'application/json')
            );
        }
    }
}