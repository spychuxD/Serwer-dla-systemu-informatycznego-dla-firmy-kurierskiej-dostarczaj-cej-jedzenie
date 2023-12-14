<?php

namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecurityController  extends AbstractController
{
    #[Route('api/public/decodeToken', name: 'api_decode_token', methods: 'POST')]
    public function decodeToken(Request $request, JWTEncoderInterface $jwtEncoder, $authorizationHeader)
    {
//        dump($authorizationHeader);die;
        $token = $request->headers->get('Authorization');
        // Usuń "Bearer " z tokena
        $token = str_replace('Bearer ', '', $token);

        try {
            $data = $jwtEncoder->decode($token);

            // Tutaj masz dostęp do danych z odszyfrowanego tokena
//            $email = $data['email'];

            // Teraz możesz użyć emaila do znalezienia użytkownika w bazie danych
            // Przykład:
            // $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

            return new JsonResponse($data);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid token'], 401);
        }
    }
}
