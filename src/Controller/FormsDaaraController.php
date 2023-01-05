<?php

namespace App\Controller;

use ApiPlatform\Core\Annotation\ApiResource;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
)]

class FormsDaaraController extends AbstractController
{

    private $client;
    public $em;
    public function __construct(HttpClientInterface $client, EntityManagerInterface $em)
    {
        $this->client = $client;
        $this->em = $em;
    }

    /**
     * @Route("/api/formsdaaras", name="app_formsdaara_get_post", methods={"GET", "POST"})
     */

    public function formulairesDaara(Request $request): Response
    {

        try {
            $MYAGROPULSE_FORM_BASEURI = $this->getParameter('app.MYAGROPULSE_FORM_BASEURI');
            $response = null;


            if ($request->getMethod() == 'POST') {

                $datas = json_decode($request->getContent(), true);

                $trime = trim($datas['title']);
                $title = strtolower(str_replace(' ', '_', $trime));

                $sections = $datas["section"];

                foreach ($sections as $section) {

                    $table = $title . "_" . $section['title'];
                    $fields = $section['fields'];
                    $temoin = $this->get_table_names($title);

                    //debut if
                    if ($temoin == 0) {
                        $SQL_CREATE_TABLE = "CREATE TABLE " . $title . " ( id int NOT NULL AUTO_INCREMENT PRIMARY KEY) ";
                        try {
                            $connCT = $this->em->getConnection();
                            $stmtCT = $connCT->prepare($SQL_CREATE_TABLE);
                            $resultSetCT = $stmtCT->executeQuery();
                            $rCT = $resultSetCT->fetchAllAssociative();
                            foreach ($fields as $k) {
                                $full_name_colonne = $table . '_' . $k['key'];

                                $col_names_existe = $this->get_column_names($title);
                                $no_col = array_diff([$full_name_colonne], $col_names_existe);
                                if (count($no_col) > 0) {

                                    if ($k['type'] == "Text" || $k['type'] == "CheckBox" || $k['type'] == "Hidden" ||  $k['type'] == "Email" || $k['type'] == "Phone" || $k['type'] == "Password") {
                                        $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " VARCHAR(255) ";
                                    } else if ($k['type'] == "Number" || $k['type'] == "Range") {

                                        $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " FLOAT ";
                                    } else if ($k['type'] == "Textarea") {

                                        $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " LONGTEXT ";
                                    } else if ($k['type'] == "Date" || $k['type'] == "DateTime" || $k['type'] == "Time") {

                                        $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " DATETIME ";
                                    } else if ($k['type'] == "Select" || $k['type'] == "RadioButton" || $k['type'] == "Enum") {

                                        $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " VARCHAR(255) ";
                                    } else if ($k['type'] == "POINT") {

                                        $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " .  $full_name_colonne . " POINT SRID 0 ";
                                    } else if ($k['type'] == "File" || $k['type'] == "Image") {

                                        $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " .  $full_name_colonne . " BLOB ";
                                    } else {

                                        $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " .  $full_name_colonne . " VARCHAR(255) ";
                                    }
                                    try {
                                        $connCC = $this->em->getConnection();
                                        $stmtCC = $connCC->prepare($SQL_CREATE_CHAMP);
                                        $resultSetCC = $stmtCC->executeQuery();
                                        $rCC = $resultSetCC->fetchAllAssociative();
                                    } catch (Exception $e) {
                                        echo "\nWarning " . $SQL_CREATE_CHAMP . " already exists\n";
                                        echo $e->getMessage();
                                    }
                                }
                            }
                        } catch (\Throwable $th) {
                            echo "\nWarning " . $SQL_CREATE_TABLE . " not match \n";
                            echo $th->getMessage();
                        }
                    } else {
                        foreach ($fields as $k) {
                            $full_name_colonne = $table . '_' . $k['key'];

                            $col_names_existe = $this->get_column_names($title);
                            $no_col = array_diff([$full_name_colonne], $col_names_existe);

                            if (count($no_col) > 0) {

                                if ($k['type'] == "Text" || $k['type'] == "CheckBox" || $k['type'] == "Hidden" ||  $k['type'] == "Email" || $k['type'] == "Phone" || $k['type'] == "Password") {
                                    $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " VARCHAR(255) ";
                                } else if ($k['type'] == "Number" || $k['type'] == "Range") {

                                    $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " FLOAT ";
                                } else if ($k['type'] == "Textarea") {

                                    $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " LONGTEXT ";
                                } else if ($k['type'] == "Date" || $k['type'] == "DateTime" || $k['type'] == "Time") {

                                    $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " DATETIME ";
                                } else if ($k['type'] == "Select" || $k['type'] == "RadioButton" || $k['type'] == "Enum") {
                                    $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " VARCHAR(255) ";
                                } else if ($k['type'] == "POINT") {

                                    $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " POINT SRID 0 ";
                                } else if ($k['type'] == "File" || $k['type'] == "Image") {

                                    $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " BLOB ";
                                } else {

                                    $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " VARCHAR(255) ";
                                }
                                try {
                                    $connCC = $this->em->getConnection();
                                    $stmtCC = $connCC->prepare($SQL_CREATE_CHAMP);
                                    $resultSetCC = $stmtCC->executeQuery();
                                    $rCC = $resultSetCC->fetchAllAssociative();
                                } catch (Exception $e) {
                                    echo "\nWarning " . $SQL_CREATE_CHAMP . " already exists\n";
                                    echo $e->getMessage();
                                }
                            }
                        }
                    }
                } // fin create table

                $response = $this->client->request($request->getMethod(), $MYAGROPULSE_FORM_BASEURI . '/formsdaaras', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept: application/json',

                    ], 'json' => json_decode($request->getContent(), true)
                ]);
            } elseif ($request->getMethod() == 'DELETE') {
                dd($request);

                $url = `$MYAGROPULSE_FORM_BASEURI/formsdaaras/`;
                $response = $this->client->request($request->getMethod(), $MYAGROPULSE_FORM_BASEURI . '/formsdaaras', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept: application/json',

                    ], 'json' => json_decode($request->getContent(), true)
                ]);
            } elseif ($request->getMethod() == 'GET') {
                $response = $this->client->request(
                    $request->getMethod(),
                    $MYAGROPULSE_FORM_BASEURI . '/formsdaaras'
                );
            }

            $statusCode = $response->getStatusCode();
            $content = $response->toArray(FALSE);
            return new JsonResponse($content, $statusCode);
        } catch (\Exception $e) {
            return new JsonResponse(['err' => $e->getMessage()], 200);
        }
    }


    /**
     * @Route("/api/formsdaaras/{id}", name="app_formsdaara", methods={ "GET","POST", "DELETE" , "PUT"})
     */

    public function formulairesDaaraUD(Request $request, string $id): Response
    {

        try {
            $MYAGROPULSE_FORM_BASEURI = $this->getParameter('app.MYAGROPULSE_FORM_BASEURI');
            $response = null;

            if ($request->getMethod() == 'POST') {

                $datas = json_decode($request->getContent(), true);

                $trime = trim($datas['title']);
                $title = strtolower(str_replace(' ', '_', $trime));

                $sections = $datas["section"];

                foreach ($sections as $section) {


                    $table = $title . "_" . $section['title'];
                    $fields = $section['fields'];
                    $temoin = $this->get_table_names($title);

                    //debut if
                    if ($temoin == 0) {
                        $SQL_CREATE_TABLE = "CREATE TABLE " . $title . " ( id int NOT NULL AUTO_INCREMENT PRIMARY KEY) ";
                        try {
                            $connCT = $this->em->getConnection();
                            $stmtCT = $connCT->prepare($SQL_CREATE_TABLE);
                            $resultSetCT = $stmtCT->executeQuery();
                            $rCT = $resultSetCT->fetchAllAssociative();
                            foreach ($fields as $k) {
                                $full_name_colonne = $table . '_' . $k['key'];

                                $col_names_existe = $this->get_column_names($title);
                                $no_col = array_diff([$full_name_colonne], $col_names_existe);

                                dd(
                                    $title,
                                    $full_name_colonne
                                );

                                if (count($no_col) > 0) {

                                    if ($k['type'] == "Text" || $k['type'] == "CheckBox" || $k['type'] == "Hidden" ||  $k['type'] == "Email" || $k['type'] == "Phone" || $k['type'] == "Password") {
                                        $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " VARCHAR(255) ";
                                    } else if ($k['type'] == "Number" || $k['type'] == "Range") {

                                        $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " FLOAT ";
                                    } else if ($k['type'] == "Textarea") {

                                        $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " LONGTEXT ";
                                    } else if ($k['type'] == "Date" || $k['type'] == "DateTime" || $k['type'] == "Time") {

                                        $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " DATETIME ";
                                    } else if ($k['type'] == "Select" || $k['type'] == "RadioButton" || $k['type'] == "Enum") {

                                        $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " VARCHAR(255) ";
                                    } else if ($k['type'] == "POINT") {

                                        $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " .  $full_name_colonne . " POINT SRID 0 ";
                                    } else if ($k['type'] == "File" || $k['type'] == "Image") {

                                        $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " .  $full_name_colonne . " BLOB ";
                                    } else {

                                        $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " .  $full_name_colonne . " VARCHAR(255) ";
                                    }
                                    try {
                                        $connCC = $this->em->getConnection();
                                        $stmtCC = $connCC->prepare($SQL_CREATE_CHAMP);
                                        $resultSetCC = $stmtCC->executeQuery();
                                        $rCC = $resultSetCC->fetchAllAssociative();
                                    } catch (Exception $e) {
                                        echo "\nWarning " . $SQL_CREATE_CHAMP . " already exists\n";
                                        echo $e->getMessage();
                                    }
                                }
                            }
                        } catch (\Throwable $th) {
                            echo "\nWarning " . $SQL_CREATE_TABLE . " not match \n";
                            echo $th->getMessage();
                        }
                    } else {

                        foreach ($fields as $k) {
                            $full_name_colonne = $table . '_' . $k['key'];

                            $col_names_existe = $this->get_column_names($title);
                            $no_col = array_diff([$full_name_colonne], $col_names_existe);

                            if (count($no_col) > 0) {

                                if ($k['type'] == "Text" || $k['type'] == "CheckBox" || $k['type'] == "Hidden" ||  $k['type'] == "Email" || $k['type'] == "Phone" || $k['type'] == "Password") {
                                    $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " VARCHAR(255) ";
                                } else if ($k['type'] == "Number" || $k['type'] == "Range") {

                                    $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " FLOAT ";
                                } else if ($k['type'] == "Textarea") {

                                    $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " LONGTEXT ";
                                } else if ($k['type'] == "Date" || $k['type'] == "DateTime" || $k['type'] == "Time") {

                                    $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " DATETIME ";
                                } else if ($k['type'] == "Select" || $k['type'] == "RadioButton" || $k['type'] == "Enum") {
                                    $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " VARCHAR(255) ";
                                } else if ($k['type'] == "POINT") {

                                    $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " POINT SRID 0 ";
                                } else if ($k['type'] == "File" || $k['type'] == "Image") {

                                    $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " BLOB ";
                                } else {

                                    $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " VARCHAR(255) ";
                                }
                                try {
                                    $connCC = $this->em->getConnection();
                                    $stmtCC = $connCC->prepare($SQL_CREATE_CHAMP);
                                    $resultSetCC = $stmtCC->executeQuery();
                                    $rCC = $resultSetCC->fetchAllAssociative();
                                } catch (Exception $e) {
                                    echo "\nWarning " . $SQL_CREATE_CHAMP . " already exists\n";
                                    echo $e->getMessage();
                                }
                            }
                        }
                    }
                }

                $response = $this->client->request($request->getMethod(), $MYAGROPULSE_FORM_BASEURI . '/formsdaaras', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept: application/json',

                    ], 'json' => json_decode($request->getContent(), true)
                ]);
            } elseif ($request->getMethod() == 'GET') {

                $response = $this->client->request($request->getMethod(), $MYAGROPULSE_FORM_BASEURI . '/formsdaaras/' . $id, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept: application/json',
                    ]
                ]);
            } elseif ($request->getMethod() == 'DELETE') {


                $response = $this->client->request($request->getMethod(), $MYAGROPULSE_FORM_BASEURI . '/formsdaaras/' . $id, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept: application/json',
                    ]
                ]);
            } elseif ($request->getMethod() == 'PUT') {
                $datas = json_decode($request->getContent(), true);

                $trime = trim($datas['title']);
                $title = strtolower(str_replace(' ', '_', $trime));

                $sections = $datas["section"];

                foreach ($sections as $section) {


                    $table = $title . "_" . $section['title'];
                    $fields = $section['fields'];
                    $temoin = $this->get_table_names($title);

                    //debut if
                    if ($temoin == 0) {
                        $SQL_CREATE_TABLE = "CREATE TABLE " . $title . " ( id int NOT NULL AUTO_INCREMENT PRIMARY KEY) ";
                        try {
                            $connCT = $this->em->getConnection();
                            $stmtCT = $connCT->prepare($SQL_CREATE_TABLE);
                            $resultSetCT = $stmtCT->executeQuery();
                            $rCT = $resultSetCT->fetchAllAssociative();
                            foreach ($fields as $k) {
                                $full_name_colonne = $table . '_' . $k['key'];

                                $col_names_existe = $this->get_column_names($title);
                                $no_col = array_diff([$full_name_colonne], $col_names_existe);

                                dd(
                                    $title,
                                    $full_name_colonne
                                );

                                if (count($no_col) > 0) {

                                    if ($k['type'] == "Text" || $k['type'] == "CheckBox" || $k['type'] == "Hidden" ||  $k['type'] == "Email" || $k['type'] == "Phone" || $k['type'] == "Password") {
                                        $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " VARCHAR(255) ";
                                    } else if ($k['type'] == "Number" || $k['type'] == "Range") {

                                        $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " FLOAT ";
                                    } else if ($k['type'] == "Textarea") {

                                        $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " LONGTEXT ";
                                    } else if ($k['type'] == "Date" || $k['type'] == "DateTime" || $k['type'] == "Time") {

                                        $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " DATETIME ";
                                    } else if ($k['type'] == "Select" || $k['type'] == "RadioButton" || $k['type'] == "Enum") {

                                        $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " VARCHAR(255) ";
                                    } else if ($k['type'] == "POINT") {

                                        $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " .  $full_name_colonne . " POINT SRID 0 ";
                                    } else if ($k['type'] == "File" || $k['type'] == "Image") {

                                        $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " .  $full_name_colonne . " BLOB ";
                                    } else {

                                        $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " .  $full_name_colonne . " VARCHAR(255) ";
                                    }
                                    try {
                                        $connCC = $this->em->getConnection();
                                        $stmtCC = $connCC->prepare($SQL_CREATE_CHAMP);
                                        $resultSetCC = $stmtCC->executeQuery();
                                        $rCC = $resultSetCC->fetchAllAssociative();
                                    } catch (Exception $e) {
                                        echo "\nWarning " . $SQL_CREATE_CHAMP . " already exists\n";
                                        echo $e->getMessage();
                                    }
                                }
                            }
                        } catch (\Throwable $th) {
                            echo "\nWarning " . $SQL_CREATE_TABLE . " not match \n";
                            echo $th->getMessage();
                        }
                    } else {

                        foreach ($fields as $k) {
                            $full_name_colonne = $table . '_' . $k['key'];

                            $col_names_existe = $this->get_column_names($title);
                            $no_col = array_diff([$full_name_colonne], $col_names_existe);

                            if (count($no_col) > 0) {

                                if ($k['type'] == "Text" || $k['type'] == "CheckBox" || $k['type'] == "Hidden" ||  $k['type'] == "Email" || $k['type'] == "Phone" || $k['type'] == "Password") {
                                    $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " VARCHAR(255) ";
                                } else if ($k['type'] == "Number" || $k['type'] == "Range") {

                                    $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " FLOAT ";
                                } else if ($k['type'] == "Textarea") {

                                    $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " LONGTEXT ";
                                } else if ($k['type'] == "Date" || $k['type'] == "DateTime" || $k['type'] == "Time") {

                                    $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " DATETIME ";
                                } else if ($k['type'] == "Select" || $k['type'] == "RadioButton" || $k['type'] == "Enum") {
                                    $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " VARCHAR(255) ";
                                } else if ($k['type'] == "POINT") {

                                    $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " POINT SRID 0 ";
                                } else if ($k['type'] == "File" || $k['type'] == "Image") {

                                    $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " BLOB ";
                                } else {

                                    $SQL_CREATE_CHAMP = " ALTER TABLE " . $title . " ADD " . $full_name_colonne . " VARCHAR(255) ";
                                }
                                try {
                                    $connCC = $this->em->getConnection();
                                    $stmtCC = $connCC->prepare($SQL_CREATE_CHAMP);
                                    $resultSetCC = $stmtCC->executeQuery();
                                    $rCC = $resultSetCC->fetchAllAssociative();
                                } catch (Exception $e) {
                                    echo "\nWarning " . $SQL_CREATE_CHAMP . " already exists\n";
                                    echo $e->getMessage();
                                }
                            }
                        }
                    }
                }

                $response = $this->client->request($request->getMethod(), $MYAGROPULSE_FORM_BASEURI . '/formsdaaras/' . $id, [
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


    /**
     * @Route("/api/formsdaaras/title/{title}", name="app_formsdaaras_find_by_title",methods={"GET"})
     */
    public function oneByTitleDaaras(string $title): Response
    {
        $MYAGROPULSE_FORM_BASEURI = $this->getParameter('app.MYAGROPULSE_FORM_BASEURI');

        // $title = json_decode($request->getContent(), true)['title'];

        $uri = "$MYAGROPULSE_FORM_BASEURI/formsdaaras/title/$title";

        try {
            $response = null;
            $response = $this->client->request('GET', $uri, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept: application/json',
                ]
            ]);
            $statusCode = $response->getStatusCode();
            $content = $response->toArray(FALSE);
            return new JsonResponse($content, $statusCode);
        } catch (\Exception $e) {
            return new JsonResponse(['err' => $e->getMessage()], 500);
        }
    }

    //fonction retournant 0 si la table n'exite pas , sinon autre chose 
    public  function get_table_names($table)
    {
        $sql =   "SELECT count(*) FROM information_schema.TABLES WHERE (TABLE_SCHEMA = 'daaras') AND (TABLE_NAME = '" . $table . "' )";

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
    ///fonction retournant les colonnes 
    public  function get_column_names($table)
    {
        $sql = 'DESCRIBE ' . $table;
        try {
            $conn = $this->em->getConnection();
            $stmt = $conn->prepare($sql);
            $resultSet = $stmt->executeQuery();
            $r = $resultSet->fetchAllAssociative();
        } catch (Exception $e) {
            throw new \RuntimeException(sprintf('Error while preparing the SQL: %s', $e->getMessage()));
        }
        $rows = array();
        foreach ($r as $rr) {
            $rows[] = $rr['Field'];
        }
        return $rows;
    }
    //function validate type date
    function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    //function if validate date
    function checkIsAValidDate($myDateString)
    {
        return (bool)strtotime($myDateString);
    }

    //function remove space
    function removeSpace($name)
    {
        for ($i = 0; $i < strlen($name); $i++) {
            if ($name[$i] == " ") {
                $name = substr($name, 1, strlen($name));
            }
        }

        return $name;
    }
}