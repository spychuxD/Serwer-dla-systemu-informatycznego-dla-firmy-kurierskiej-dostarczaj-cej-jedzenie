<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\AddressRepository;
use App\Repository\UserDataRepository;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private UserRepository $userRepository;
    private UserDataRepository $userDataRepository;
    private AddressRepository $addressRepository;

    public function __construct(UserRepository $userRepository, UserDataRepository $userDataRepository, AddressRepository $adddressRepository) {
        $this->userRepository = $userRepository;
        $this->userDataRepository = $userDataRepository;
        $this->addressRepository = $adddressRepository;
    }

    #[Route('common/registration', name: 'common_registration', methods: 'POST')]
    public function index(Request $request): Response
    {
        $content = $request->getContent();
        $tmp = json_decode($content, true);
        $user = $tmp['userRegister'];

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
            $existingUser = $this->userRepository->getUserByEmail($user['email']);
            $result = $this->userDataRepository->addUserData($user, $existingUser[0]);
            if ($result === 0) {
                return new Response(
                    json_encode(array('Pomyślnie zarejestrowano użytkownika do systemu, lecz adres został podany nieprawidłowo, możesz go zmienić w edycji profilu.')),
                    207,
                    array('content-type' => 'application/json')
                );
            }
            if ($result === 1) {
                return new Response(
                    json_encode(array('Nieprzewidziany wyjątek, ponieważ system próbuje dodać dane dla nieistniejacego adresu email. Sprawdź poprawność danych logowania.')),
                    207,
                    array('content-type' => 'application/json')
                );
            }
            return new Response(
                json_encode(array('Pomyślnie zarejestrowano użytkownika do systemu.')),
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

    #[Route('common/updateUserToken', name: 'common_update_user_token', methods: 'POST')]
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

    #[Route('api/admin/getUsers', name: 'api_admin_get_user', methods: 'GET')]
    public function getIngridientsCategories(): Response
    {
        $users = $this->userRepository->getUsers();
        if(empty($users)) {
            return new Response(
                json_encode(array('message'=>'Brak kategorii')),
                207,
                array('content-type' => 'application/json')
            );
        }
        return new Response(
            json_encode($users),
            200,
            array('content-type' => 'application/json')
        );
    }
}
