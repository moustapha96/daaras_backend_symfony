<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatController extends AbstractController
{
    public function __construct()
    {
    }


    // existance element
    public function isExite(string $nom, array $tableau)
    {

        foreach ($tableau as $v) {
            if ($v['name'] == $nom) {
                return true;
            }
        }
        return false;
    }

    //fonction return series
    public function getSerie(string $nom, array $tableau)
    {
        $resultats = [];
        foreach ($tableau as $value) {
            if ($nom == $value['produit']) {
                $resultats[] = ['value' => $value['quantiteTotal'], 'name' => $value['date']];
            }
        }
        return $resultats;
    }

}
