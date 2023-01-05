<?php

namespace App\Controller;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
)]
class FormulairesController extends AbstractController
{

    private $client;
    public $em;
    public function __construct(HttpClientInterface $client, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->client = $client;
    }


    /**
     * @Route("/api/formulaires", name="app_formulaires", methods={"GET", "POST"})
     */

    public function formulaires(Request $request): Response
    {
        try {
            $MYAGROPULSE_FORM_BASEURI = $this->getParameter('app.MYAGROPULSE_FORM_BASEURI');
            $response = null;
            if ($request->getMethod() == 'POST') {

                $response = $this->client->request($request->getMethod(), $MYAGROPULSE_FORM_BASEURI . '/formulaires', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept: application/json',

                    ], 'json' => json_decode($request->getContent(), true)
                ]);

                // $datas = json_decode($request->getContent(), true);
                // $table = strtolower(str_replace(' ', '_', $datas['title']));
                // $temoin = $this->get_table_names($table);


                // if ($temoin != 0) {
                //     $SQL_IF_EXISTE = "CREATE TABLE " . $table . " ( id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY) ";
                //     $SQL_UPDATE_ID = "ALTER TABLE  " . $table . "MODIFY id  int(11) NOT NULL AUTO_INCREMENT ";
                //     try {
                //         $conn2 = $this->em->getConnection();
                //         $stmt2 = $conn2->prepare($SQL_IF_EXISTE);
                //         $resultSet2 = $stmt2->executeQuery();
                //         $r2 = $resultSet2->fetchAllAssociative();

                //         try {
                //             $connUID = $this->em->getConnection();
                //             $stmtUID = $connUID->prepare($SQL_UPDATE_ID);
                //             $resultUID = $stmtUID->executeQuery();
                //             $rUID = $resultUID->fetchAllAssociative();
                //         } catch (Exception $ee) {
                //             echo "\n Warning ID NO AUTO INCREMENT \n";
                //             echo $ee->getMessage();
                //         }
                //     } catch (Exception $e) {
                //         echo "\n Warning table already exists\n";
                //         echo $e->getMessage();
                //     }
                // }
            } elseif ($request->getMethod() == 'GET') {
                $response = $this->client->request(
                    $request->getMethod(),
                    $MYAGROPULSE_FORM_BASEURI . '/formulaires'
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
     * @Route( "/api/formulaires/menu", name="app_formulaires_menu" , methods={"GET"}  )
     */
    public function formulairesMenu(Request $request): Response
    {

        try {
            $MYAGROPULSE_FORM_BASEURI = $this->getParameter('app.MYAGROPULSE_FORM_BASEURI');
            $response = null;
            $uri = "$MYAGROPULSE_FORM_BASEURI/formulaires/menu";

            $response = $this->client->request($request->getMethod(), $uri, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept: application/json',
                ]
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->toArray(FALSE);
            return new JsonResponse($content, $statusCode);
        } catch (\Exception $e) {
            return new JsonResponse(['err' => $e->getMessage()], 200);
        }
    }


    /**
     * @Route("/api/formulaires/logo", name="app_formulaires_logo", methods={"POST"})
     */

    public function formulaireslogo(Request $request): Response
    {
        try {
            $MYAGROPULSE_FORM_BASEURI = $this->getParameter('app.MYAGROPULSE_FORM_BASEURI');
            $response = null;
            if ($request->getMethod() == 'POST') {

                $response = $this->client->request($request->getMethod(), $MYAGROPULSE_FORM_BASEURI . '/formulaires/logo', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept: application/json',
                    ], 'json' => json_decode($request->getContent(), true)
                ]);
            }

            $statusCode = $response->getStatusCode();
            $content = $response->toArray(FALSE);
            return new JsonResponse($content, $statusCode);
        } catch (\Exception $e) {
            return new JsonResponse(['err' => $e->getMessage()], 200);
        }
    }

    //fonction retournant 0 si la table n'exite pas , sinon autre chose 
    public  function get_table_names($table)
    {
        $sql =   "SELECT count(*) FROM information_schema.TABLES WHERE (TABLE_SCHEMA = 'laiterie') AND (TABLE_NAME = '" . $table . "' )";

        try {
            $conn = $this->em->getConnection();
            $stmt = $conn->prepare($sql);
            $resultSet = $stmt->executeQuery();
            $r = $resultSet->fetchAllAssociative();
            return $r[0]['count(*)'];
        } catch (Exception $e) {
            throw new \RuntimeException(sprintf('Error while preparing the SQL: %s', $e->getMessage()));
        }
    }



    /**
     * @Route("/api/formulaires/title", name="app_formulaire_find_title",methods={"POST"})
     */
    public function oneByTitle(Request $request): Response
    {
        $MYAGROPULSE_FORM_BASEURI = $this->getParameter('app.MYAGROPULSE_FORM_BASEURI');

        $title = json_decode($request->getContent(), true)['title'];

        $uri = "$MYAGROPULSE_FORM_BASEURI/formulaires/title/$title";
        try {
            $response = null;
            if ($request->getMethod() == 'POST') {

                $response = $this->client->request('GET', $uri, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept: application/json',
                    ]
                ]);
            }

            $statusCode = $response->getStatusCode();
            $content = $response->toArray(FALSE);
            return new JsonResponse($content, $statusCode);
        } catch (\Exception $e) {
            return new JsonResponse(['err' => $e->getMessage()], 500);
        }
    }

    /**
     * @Route("/api/formulaires/{id}", name="app_formulaire",methods={"GET", "POST", "PUT", "DELETE"})
     */
    public function byOneformulaires(Request $request, String $id): Response
    {
        $MYAGROPULSE_FORM_BASEURI = $this->getParameter('app.MYAGROPULSE_FORM_BASEURI');
        $uri = "$MYAGROPULSE_FORM_BASEURI/formulaires/$id";
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

    /**
     * @Route("/api/data", name="app_data", methods={"GET", "POST"})
     */
    public function data(Request $request): Response
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
     *@Route("/api/allTable", name="api_all_table",methods={"GET"}  )
     */
    public function getAllTable(EntityManagerInterface $em): Response
    {

        $entities = $em->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();
        $res = [];
        foreach ($entities as $en) {
            $res[] = $this->convertClassNameToShortcutNotations($en);
        }
        $resultats = [];
        foreach ($res as $r) {

            $lastChar = substr($r, -1);
            if ($lastChar != 's') $r .= 's';
            $resultats[] = strtolower($r);
        }

        return  new JsonResponse($res);
    }

    public function convertClassNameToShortcutNotations($className)
    {
        $cleanClassName = str_replace('App\\Entity\\', '', $className);
        $parts = explode('\\', $cleanClassName);

        return implode('', $parts);
    }

    /**    
     *@Route("/api/getData", name="api_get_data_table",methods={"POST"}  )
     */
    public function getData(Request $request, ManagerRegistry $doctrine, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        $t =  $data['name'];
        $s =   "App\\Entity\\{$t}";
        $res = $doctrine->getRepository($s)->findAll();
        $resultat = [];
        foreach ($res as $r) {
            $resultat[] = $r->asArray();
        }
        return new JsonResponse($resultat);
    }
}