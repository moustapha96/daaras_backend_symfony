<?php


namespace App\Controller;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Driver\PDO\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;


use Symfony\Component\Routing\Annotation\Route;

class MyDynamicController extends AbstractController
{

    public $em;
    public function __construct(HttpClientInterface $client, EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    //fonction renvoyant la liste d'une table données
    /**
     * @Route("/api/getData/{formulaire}",name="app_tables_formulaire", methods={"GET"})
     */
    public function getTablesFormulaire(string $formulaire): Response
    {


        $title = str_replace(' ', '_', strtolower($formulaire));
        $sql_r = "show tables LIKE '" . $title . "_%'";

        try {
            $conn = $this->em->getConnection();
            $stmt = $conn->prepare($sql_r);
            $resultSet = $stmt->executeQuery();
            $r = $resultSet->fetchAllAssociative();
            $res = [];
            foreach ($r as $i => $rr) {
                foreach ($rr as $ii => $d) {
                    $res[] = $d;
                }
            }
            return new JsonResponse($res, 200);
        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), 200);
        }
    }


    /**
     * @Route("/api/search_datas",name="app_dynamic_search", methods={"POST"})
     */
    public function searchInEntity(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        // $table = $data['table'];
        $table = strtolower(str_replace(' ', '_', $data['table']));
        $data_table = $data['data'];
        $keys = array_keys($data_table);
        $vv = "";

        for ($i = 0; $i < count($keys); $i++) {
            if ($i <= count($keys) - 2) {
                $vv .= "" . $keys[$i] . " = '" . $data_table[$keys[$i]] . "' AND ";
            } else  $vv .= "" . $keys[$i] . " = " . $data_table[$keys[$i]];
        }

        $SQL_INSERT = "SELECT * FROM  " . $table . " where " .  $vv;

        try {
            $connT = $this->em->getConnection();
            $stmtT = $connT->prepare($SQL_INSERT);
            $resultSetT = $stmtT->executeQuery();
            $rT = $resultSetT->fetchAllAssociative();
            return new JsonResponse([$rT]);
        } catch (Exception $e) {
            return new JsonResponse([$e->getMessage()]);
        }
        return new JsonResponse(['pas de données ']);
    }

    /**
     * @Route("/api/get_datas/{table}",name="app_dynamic_get_data", methods={"GET"})
     */
    public function getDataOfTable(string $table): Response
    {
        if ($this->get_table_names($table) != 0) {
            return new JsonResponse([], 200);
        }
        $SQL_INSERT = "SELECT * FROM  " . $table;

        try {
            $connT = $this->em->getConnection();
            $stmtT = $connT->prepare($SQL_INSERT);
            $resultSetT = $stmtT->executeQuery();
            $rT = $resultSetT->fetchAllAssociative();
            return new JsonResponse($rT, 200);
        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), 200);
        }
        return new JsonResponse([], 200);
    }

    /**
     * @Route("/api/delete_datas",name="app_dynamic_delete", methods={"POST"})
     */
    public function deleteData(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        // $table = $data['table'];
        $table = strtolower(str_replace(' ', '_', $data['table']));
        $id = $data['id'];
        $sql = "DELETE FROM " . $table . " WHERE id = '" . $id . "'";
        try {
            $connT = $this->em->getConnection();
            $stmtT = $connT->prepare($sql);
            $resultSetT = $stmtT->executeQuery();
            $rT = $resultSetT->fetchAllAssociative();
            return new JsonResponse(['donnée supprimée']);
        } catch (Exception $e) {
            return new JsonResponse([$e->getMessage()]);
        }
    }
    /**
     * @Route("/api/delete_table/{table}",name="app_dynamic_delete_table", methods={"GET"})
     */
    public function deleteTable(string $table): Response
    {
        $sql = "DROP TABLE " . $table;
        try {
            $connT = $this->em->getConnection();
            $stmtT = $connT->prepare($sql);
            $resultSetT = $stmtT->executeQuery();
            $rT = $resultSetT->fetchAllAssociative();
            return new JsonResponse(['table supprimée']);
        } catch (Exception $e) {
            return new JsonResponse([$e->getMessage()]);
        }
    }


    /**
     * @Route("/api/save_datas",name="app_dynamic_post", methods={"POST"})
     */
    public function createDynamicEntity(Request $request): Response
    {

        $data =  json_decode($request->getContent(), true);
        $keys =  array_keys($data);

        foreach ($keys as $k) {
            $tab = explode('_', $k);
            $name_table = $tab[0];
        }
        if ($this->get_table_names($name_table) == 0) {
            $this->createTable($name_table);
        }
        foreach ($keys as $col) {
            $donnee = $data[$col];

            if ($this->checkIsAValidDate($donnee)  == true) {
                $SQL_CREATE_CHAMP = " ALTER TABLE " . $name_table . " ADD " . $col . " DATETIME ";
            } elseif (is_int($donnee)) {
                $SQL_CREATE_CHAMP = " ALTER TABLE " . $name_table . " ADD " . $col . " INTEGER ";
            } elseif (is_bool($donnee)) {
                $SQL_CREATE_CHAMP = " ALTER TABLE " . $name_table . " ADD " . $col . " BOOLEAN ";
            } elseif (is_float($donnee)) {
                $SQL_CREATE_CHAMP = " ALTER TABLE " . $name_table . " ADD " . $col . " FLOAT ";
            } elseif (is_string($donnee)) {
                $SQL_CREATE_CHAMP = " ALTER TABLE " . $name_table . " ADD " . $col . " VARCHAR(255) ";
            }

            $collones_table =  $this->get_column_names($name_table);
            if (in_array($col,  $collones_table) == false) {
                $this->createColonnetable($SQL_CREATE_CHAMP);
            }
        }

        $data_string = "";
        $collone_string = "";

        foreach ($keys as $i => $k) {

            $donnee = $data[$k];
            if ($this->checkIsAValidDate($donnee)  == true) {
                $date = date('Y-m-d H:i:s', strtotime($donnee));
                $data_string .= "'" . $date . "',";
            } else {
                $data_string .= "'" . $donnee . "',";
            }
            $collone_string .= $k . ",";
        }
        $SQL_INSERT = "INSERT INTO " . $name_table . " (" . $collone_string . ")  VALUES ("  . $data_string . ")";
        $SQL_INSERT = str_replace(",)", ")", $SQL_INSERT);


        if ($SQL_INSERT != '') {
            try {
                $conn = $this->em->getConnection();
                $stmt = $conn->prepare($SQL_INSERT);
                $resultSet = $stmt->executeQuery();
                $r2 = $resultSet->fetchAllAssociative();
                // return new JsonResponse(["reussie"]);
            } catch (Exception $e) {
                return new JsonResponse([$e->getMessage()]);
            }
        }

        return new JsonResponse("insertion treussie", 201);
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


    function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    function checkIsAValidDate($myDateString)
    {
        return (bool)strtotime($myDateString);
    }
    //fonction retournant 0 si la table n'exite pas , sinon autre chose 
    function get_table_names($table)
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

    function removeSpace($name)
    {
        for ($i = 0; $i < strlen($name); $i++) {
            if ($name[$i] == " ") {
                $name = substr($name, 1, strlen($name));
            }
        }

        return $name;
    }
    //creation d'une table
    function createTable(string $name_table)
    {
        if ($this->get_table_names($name_table) == 0) {
            $SQL_CREATE_TABLE = "CREATE TABLE " . $name_table . " ( id int NOT NULL AUTO_INCREMENT PRIMARY KEY) ";
            try {
                $connCT = $this->em->getConnection();
                $stmtCT = $connCT->prepare($SQL_CREATE_TABLE);
                $resultSetCT = $stmtCT->executeQuery();
                $rCT = $resultSetCT->fetchAllAssociative();
                return true;
            } catch (\Throwable $th) {
                return false;
            }
        } else {
            return false;
        }
    }
    //creation colonne d'une table
    function createColonnetable(string $colonne)
    {
        try {
            $connCC = $this->em->getConnection();
            $stmtCC = $connCC->prepare($colonne);
            $resultSetCC = $stmtCC->executeQuery();
            $rCC = $resultSetCC->fetchAllAssociative();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}