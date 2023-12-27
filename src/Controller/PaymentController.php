<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Repository\PaymentRepository;
use App\Repository\RestaurantRepository;
use App\Repository\UserRepository;
use Pusher\Pusher;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Ratchet\ConnectionInterface;
use Symfony\Component\WebSocket\Server\TopicHandlerInterface;
use Symfony\Component\WebSocket\Server\ConnectionManagerInterface;

class PaymentController extends AbstractController
{
    private UserRepository $userRepository;
    private PaymentRepository $paymentRepository;
    private RestaurantRepository $restaurantRepository;

    public function __construct(UserRepository $userRepository, PaymentRepository $paymentRepository, RestaurantRepository $restaurantRepository)
    {
        $this->userRepository = $userRepository;
        $this->paymentRepository = $paymentRepository;
        $this->restaurantRepository = $restaurantRepository;
    }
    #[Route('common/payment', name: 'common_public_payment', methods: 'POST')]
    public function makePayment(Request $request, SerializerInterface $serializer, ConnectionManagerInterface $connectionManager): Response
    {
        $response = $this->forward(SecurityController::class . '::decodeToken');
        $data = json_decode($response->getContent(), true);
        $content = $request->getContent();
        $params = json_decode($content, true);
        if (array_key_exists('error', $data)) {
            if (array_key_exists('email', $params) && !empty($params['email'])) {
                $email = $params['email'];
            } else {
                return new Response(
                    json_encode(array('Nie podano adresu mailowego.')),
                    207,
                    array('content-type' => 'application/json')
                );
            }
        }
        if (!isset($email)) {
            $email = $data['username'];
            $userTemp = $this->userRepository->getUserByEmail($email);
            if (!empty($userTemp)) {
                $user = $userTemp[0];
            }
        }

//        $userTemp = $this->userRepository->getUserByEmail($email);
//        if (!empty($userTemp)) {
//            $user = $userTemp[0];
//        }
//        dump($userTemp);die;
        $oauthUrl = 'https://openapi.sandbox.tpay.com/oauth/auth';
        $clientId = '01HGDA614RF1T1J8CADAHAZEPW-01HGDAC04RRYJJ2JSSXR1R482S';
        $clientSecret = 'f38d5cd3cbfd284186526dc140bf8b206fdbdfc79dc4dcae372aab4842125d35';
        $curl = curl_init();
        $postRequest = array(
            'client_id' => $clientId,
            'client_secret' => $clientSecret
        );
        $options = array(
            CURLOPT_URL => $oauthUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query($postRequest),
            CURLOPT_SSL_VERIFYPEER => false,
        );

        foreach ($options as $key => $option) {
            curl_setopt($curl, $key, $option);
        }

        $response = curl_exec($curl);
        if ($response === false) {
            return new Response(
                json_encode(array('Curl error: ' . curl_error($curl))),
                207,
                array('content-type' => 'application/json')
            );
        }
        curl_close($curl);
        $accessToken = '';
        $oauthResponse = json_decode($response, true);
        if (array_key_exists('access_token', $oauthResponse)) {
            $accessToken = $oauthResponse['access_token'];
            $transactionUrl = 'https://openapi.sandbox.tpay.com/transactions';
            if (array_key_exists('amount', $params) && !empty($params['amount']) && array_key_exists('hiddenDescription', $params) && !empty($params['hiddenDescription'])) {
                $curlForTransaction = curl_init();
                $payer = array(
                    'email' => $email,
                    'name' => $email
                );
                $payerUrls = array(
                    'success' => 'http://localhost:8080/success',
                    'error' => 'http://localhost:8080/error'
                );
                $notification = array(
                    'url' => 'http://localhost:8080/cartSummary',
                    'email' => 'dawid.spychalski00@gmail.com'
                );
                $callbacks = array(
                    'payerUrls' => $payerUrls,
                    'notification' => $notification
                );
                $pay = array(
                    'channelId' => 64
                );
                $restaurantArray = $this->restaurantRepository->getOneRestaurantById($params['hiddenDescription']);
                $restaurant = $restaurantArray[0];
                $postRequest = array(
                    'amount' => $params['amount'],
                    'description' => $restaurant['name'],
                    'hiddenDescription' => strval($params['hiddenDescription']),
                    'payer' => $payer,
                    'callbacks' => $callbacks,
                    'pay' => $pay,
                );

                curl_setopt_array($curlForTransaction, array(
                    CURLOPT_URL => $transactionUrl,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($postRequest),
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $accessToken
                    ),
                ));

                $response = curl_exec($curlForTransaction);
                if ($response === false) {
                    return new Response(
                        json_encode(array('Curl error: ' . curl_error($curlForTransaction))),
                        207,
                        array('content-type' => 'application/json')
                    );
                }
                curl_close($curl);
                $transactionResponse = json_decode($response, true);
                if (array_key_exists('result', $transactionResponse) && $transactionResponse['result'] === 'success') {
                    $payment = new Payment();
                    $payment->setAmount($transactionResponse['amount']);
                    $payment->setEmail($email);
                    if (isset($user)) {
                        $payment->setUserId($user);
                    }
                    $payment->setStatus($transactionResponse['payments']['status']);
                    $payment->setTitle($transactionResponse['title']);
                    $payment->setTransactionId($transactionResponse['transactionId']);
                    $payment->setDateCreation($transactionResponse['date']['creation']);
                    $payment->setDateRealization($transactionResponse['date']['realization']);
                    $payment->setCurrency($transactionResponse['currency']);
                    $payment->setDescription($transactionResponse['description']);
                    $payment->setHiddenDescription($transactionResponse['hiddenDescription']);
                    $payment->setTransactionPaymentUrl($transactionResponse['transactionPaymentUrl']);
                    $errors = $this->paymentRepository->addPayment($payment);
                    if ($errors !== null) {
                        return new Response(
                            json_encode($errors),
                            207,
                            array('content-type' => 'application/json')
                        );
                    }
                } else {
                    return new Response(
                        json_encode(array('Wystąpił problem podczas tworzenia transakcji. Prawdopodobnie problem dotyczy błędnego adresu email.')),
                        207,
                        array('content-type' => 'application/json')
                    );
                }
            } else {
                return new Response(
                    json_encode(array('Nieoczekiwany problem.')),
                    207,
                    array('content-type' => 'application/json')
                );
            }
        } else {
            return new Response(
                json_encode(array('Wystąpił problem z autoryzacją. Problem dotyczy kluczy dostępowych Sprzedawcy. Tymczasowo nie można zapłacić za zamówienie.')),
                207,
                array('content-type' => 'application/json')
            );
        }
        $app_id = '1730656';
        $app_key = '17a7ae8441a823d0c153';
        $app_secret = 'ec3363acab2d93d835c4';
        $app_cluster = 'eu';

        $pusher = new Pusher($app_key, $app_secret, $app_id, ['cluster' => $app_cluster]);

        //dla http:
//        $options = [
//            'cluster' => $app_cluster,
//            'useTLS' => false
//        ];

//        $pusher = new Pusher\Pusher($app_key, $app_secret, $app_id, $options);

        $data['message'] = 'hello world';
        $pusher->trigger('orders', 'orderCreated', $data);
        return new Response(
            json_encode($transactionResponse['transactionPaymentUrl']),
            201,
            array('content-type' => 'application/json')
        );
    }
}
