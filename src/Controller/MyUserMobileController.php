<?php


namespace App\Controller;

use App\Entity\UserMobile;
use App\Repository\DepartementRepository;
use App\Repository\ProfilsRepository;
use App\Repository\RegionRepository;
use App\Repository\StatusRepository;
use App\Repository\UserMobileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyUserMobileController extends AbstractController
{


    /**
     * @Route("/api/user_mobiles/create", name="app_user_mobile_create",methods={"POST"})
     * @param Request $request
     */
    public function createUserMobile(Request $request, StatusRepository $str, RegionRepository $rr, ProfilsRepository $pr, DepartementRepository $dr, EntityManagerInterface $em): ?Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $email = $data['email'];
            $prenom = $data['prenom'];
            $nom = $data['nom'];
            $adresse = $data['adresse'];
            $telephone = $data['telephone'];
            $sexe = $data['sexe'];
            $roles = $data['roles'];
            $enabled = $data['enabled'];
            $password = $data['password'];
            $idStatus = $data['idStatus'];
            $uuid = $data['uuid'];
            $iddepartement = $data['idDepartement'];
            $departement = $dr->find($iddepartement);
            $idregion = $data['idRegion'];
            $region = $rr->find($idregion);
            $hasLaiteries = $data['hasLaiteries'];
            $idprofil = $data['idProfil'];
            $localite = $data['localite'];
            $profil = $pr->find($idprofil);
            $status = $str->find($idStatus);

            $user = new UserMobile();
            $user->setEmail($email);
            $user->setLocalite($localite);
            $user->setAdresse($adresse);
            $user->setPrenom($prenom);
            $user->setNom($nom);
            $user->setTelephone($telephone);
            $user->setSexe($sexe);
            $user->setEnabled($enabled);
            $user->setUuid($uuid);
            $user->setRegion($region);
            $user->setDepartement($departement);
            $user->setStatus($status);
            $user->setRoles($roles);
            $user->setPassword($password);
            $user->setHasLaiteries($hasLaiteries);
            $user->setProfil($profil);

            $em->persist($user);
            $em->flush();

            return new Response("user bien creer");
        } catch (\Exception $e) {
            return new Response("Laiterie non creer $e ");
        }
    }

    /**
     * @Route("/api/user_mobiles/update", name="app_user_mobile_update",methods={"POST"})
     * @param Request $request
     */
    public function updateUserMobile(Request $request, StatusRepository $str, ProfilsRepository $pr, UserMobileRepository $ru, RegionRepository $rr, DepartementRepository $dr, EntityManagerInterface $em): ?Response
    {
        try {
            $data = json_decode($request->getContent(), true);
            $id = $data['id'];
            $email = $data['email'];
            $prenom = $data['prenom'];
            $nom = $data['nom'];
            $adresse = $data['adresse'];
            $telephone = $data['telephone'];
            $sexe = $data['sexe'];
            $roles = $data['roles'];
            $enabled = $data['enabled'];
            $password = $data['password'];
            $idStatus = $data['idStatus'];
            $uuid = $data['uuid'];
            $iddepartement = $data['idDepartement'];
            $departement = $dr->find($iddepartement);
            $idregion = $data['idRegion'];
            $region = $rr->find($idregion);
            $hasLaiteries = $data['hasLaiteries'];
            $idprofil = $data['idProfil'];
            $localite = $data['localite'];
            $status = $str->find($idStatus);
            $profil = $pr->find($idprofil);


            $user = $ru->find($id);
            $user->setEmail($email);
            $user->setLocalite($localite);
            $user->setAdresse($adresse);
            $user->setPrenom($prenom);
            $user->setNom($nom);
            $user->setTelephone($telephone);
            $user->setSexe($sexe);
            $user->setEnabled($enabled);
            $user->setUuid($uuid);
            $user->setRegion($region);
            $user->setDepartement($departement);
            $user->setStatus($status);
            $user->setRoles($roles);
            $user->setPassword($password);
            $user->setHasLaiteries($hasLaiteries);
            $user->setProfil($profil);

            $em->persist($user);
            $em->flush();

            return new Response("user bien mise a jour");
        } catch (\Exception $e) {
            return new Response("user non mise a jour $e ");
        }
    }

    /**
     * @Route("/api/user_mobiles/login", name="app_user_mobile_login",methods={"POST"})
     * @param Request $request
     */
    public function loginUserMobile(Request $request, UserMobileRepository $userMobileRepository): ?Response
    {

        $data = json_decode($request->getContent(), true);
        $critera = ["email" => $data['email'], "password" => $data['password']];

        $user =  $userMobileRepository->findOneBy($critera);

        if (!$user) {
            return new JsonResponse([], 200);
        }
        return new JsonResponse($user->asArray(), 200);
    }
}