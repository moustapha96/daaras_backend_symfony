<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DataFormulairesController extends AbstractController
{

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @Route("/api/data_formulaires", name="app_data_formulaires", methods={"GET", "POST"})
     */
    public function dataFormulaires(Request $request): Response
    {
        try {
            $MYAGROPULSE_FORM_BASEURI = $this->getParameter('app.MYAGROPULSE_FORM_BASEURI');
            $response = null;
            if ($request->getMethod() == 'POST') {

                $response = $this->client->request($request->getMethod(), $MYAGROPULSE_FORM_BASEURI . '/data', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept: application/json',

                    ], 'json' => json_decode($request->getContent(), true)
                ]);
            } elseif ($request->getMethod() == 'GET') {
                $response = $this->client->request(
                    $request->getMethod(),
                    $MYAGROPULSE_FORM_BASEURI . '/data'
                );
            }

            $statusCode = $response->getStatusCode();
            $content = $response->toArray(FALSE);
            return new JsonResponse($content, $statusCode);
        } catch (\Exception $e) {
            return new JsonResponse(['err' => $e->getMessage()], 500);
        }
    }

    /**
     * @Route("/api/data_formulaires/{id}", name="app_data_formulaire_one_by", methods={"GET", "POST", "PUT", "DELETE"})
     */
    public function byOneDataFormualires(Request $request, String $id): Response
    {
        $MYAGROPULSE_FORM_BASEURI = $this->getParameter('app.MYAGROPULSE_FORM_BASEURI');
        $uri = "$MYAGROPULSE_FORM_BASEURI/data/$id";
        try {

            $response = null;
            if ($request->getMethod() == 'PUT') {
                $response = $this->client->request($request->getMethod(), $uri, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept: application/json',
                    ], 'json' => json_decode($request->getContent(), true)
                ]);
            } elseif ($request->getMethod() == 'DELETE' || $request->getMethod() == 'GET') {
                $response = $this->client->request(
                    $request->getMethod(),
                    $uri
                );
            }

            $statusCode = $response->getStatusCode();
            $content = $response->toArray(FALSE);
            return new JsonResponse($content, $statusCode);
        } catch (\Exception $e) {
            return new JsonResponse(['err' => $e->getMessage()], 500);
        }
    }
}