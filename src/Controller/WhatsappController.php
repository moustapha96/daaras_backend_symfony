<?php


declare(strict_types=1);

namespace App\Controller;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twilio\Rest\Client;


#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],

)]
class WhatsappController extends AbstractController
{


    public $em;

    public function __construct(HttpClientInterface $client, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->client = $client;
    }


    /**
     * @Route("/api/whatsapp", name="whatsapp_send_message", methods={"GET"})
     */

    public function sendMessage(): Response
    {

        $sid = $this->getParameter('TWILIO_ACCOUNT_SID');
        $token = $this->getParameter('TWILIO_AUTH_TOKEN');
        $twilio = new Client($sid, $token);


        try {
            $message = $twilio->messages
                ->create(
                    "+221784537547",
                    [
                        "body" => "Bonjour , ceci est un test",
                        // "from" => "+221774901232"
                        "from" => "+19498326669"
                    ]
                );
            print($message);
            return new JsonResponse([$message], 200);
        } catch (\Error $th) {
            // throw new Exception("Error Processing Request", 1);
            return new JsonResponse($th, 200);
        }
    }
}