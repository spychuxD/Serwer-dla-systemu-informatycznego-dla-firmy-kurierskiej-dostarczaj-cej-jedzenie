<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    #[Route('registration', name: 'api_registration', methods: 'POST')]
    public function index(Request $request): Response
    {
        $content = $request->getContent();
        $user = json_decode($content, true);

        if(!array_key_exists('email', $user) || empty($user['email']) || !array_key_exists('password', $user)
            || empty($user['password']))
        {
            return new Response(
                json_encode(array('Brak danych do logowania')),
                207,
                array('content-type' => 'application/json')
            );
        }
        $success = $this->userRepository->addUser($user);
        if ($success === 0) {
            return new Response(
                json_encode(array('Użytkownik o podanym adresie email już istnieje')),
                207,
                array('content-type' => 'application/json')
            );
        } else if (empty($success)) {
            return new Response(
                json_encode(array('Pomyślnie dodano użytkownika do systemu.')),
                201,
                array('content-type' => 'application/json')
            );
        } else {
            return new Response(
                json_encode(array('Nieprzewidziany błąd')),
                207,
                array('content-type' => 'application/json')
            );
        }
    }

    #[Route('api/public/updateUserToken', name: 'api_update_user_token', methods: 'POST')]
    public function updateUserToken(Request $request): Response
    {
        $response = $this->forward(SecurityController::class . '::decodeToken');
        $data = json_decode($response->getContent(), true);
        $email = $data['username'];
        $user = $this->userRepository->getUserByEmail($email);
        $userTemp = $user[0];
        $userTemp->setToken($request->headers->get('Authorization'));
        $this->userRepository->upgradeToken($userTemp);

        return new Response(
            json_encode(array('Upgraded')),
            201,
            array('content-type' => 'application/json')
        );
    }
}
