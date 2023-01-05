<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserMobileRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Mailer\MailerInterface;


class MyUserController extends AbstractController
{

    /**
     * @param EntityManagerInterface $entityManager
     */
    private $entityManager;
    private $passwordEncoder;
    private $userRepo;
    private $jwtManager;
    private $tokenStorageInterface;

    public function __construct(UserRepository $userRepo, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorageInterface, UserPasswordHasherInterface $passwordHasher, JWTTokenManagerInterface $jwtManager)
    {
        $this->entityManager = $entityManager;
        $this->jwtManager = $jwtManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
        $this->passwordEncoder = $passwordHasher;
        $this->userRepo = $userRepo;
    }

    /**
     * @Route("/users/register",name="create_account", methods={"POST"})
     */
    public function register(MailerInterface $mailer, Request $request, UserRepository $repo, EntityManagerInterface $entityManager): ?Response
    {
        $data  = json_decode($request->getContent(), true);
        $user = new User();
        $user->setFirstName($data['firstName']);
        $user->setEmail($data['email']);
        $user->setEnabled($data['enabled']);
        $date = new \DateTime($data['isActiveNow']);
        $datei = DateTimeImmutable::createFromMutable($date);
        $user->setLastActivityAt($datei);
        $user->setLastName($data['lastName']);
        $user->setAvatar($data['avatar']);
        $user->setPhone($data['phone']);
        $user->setRoles($data['roles']);
        $user->setStatus($data['status']);
        $user->setIsActiveNow(false);
        $hashedPassword = $this->passwordEncoder->hashPassword(
            $user,
            $data['password']
        );
        $user->setPassword($hashedPassword);
        $users = $repo->findBy(['email' => $user->getEmail()]);
        if ($users) {
            return new JsonResponse(["adresse email existe deja "], 500);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        $usersAdmin = $repo->findBy(['roles' => "ROLE_ADMIN"]);

        if (count($usersAdmin) != 0) {
            foreach ($usersAdmin as $u) {
                $email = (new TemplatedEmail())
                    ->from('simlait@pdefs.com')
                    ->to($u->getEmail())
                    ->cc($user->getEmail())
                    ->bcc('bcc@example.com')
                    ->subject('Ouverture de Compte')
                    ->htmlTemplate('main/mailOuvertureCompte.html.twig')
                    ->context([
                        'user' => $user,
                    ]);
                $mailer->send($email);
            }
        } else {
            $email = (new TemplatedEmail())
                ->from('simlait@pdefs.com')
                ->to("simlait-admin@pdefs.com")
                ->cc($user->getEmail())
                ->bcc('bcc@example.com')
                ->subject('Ouverture de Compte')
                ->htmlTemplate('main/mailOuvertureCompte.html.twig')
                ->context([
                    'user' => $user,
                ]);
            $mailer->send($email);
        }

        return new JsonResponse(["utilisateur créer avec succès "], 200);
    }

    /**
     * @Route("/api/users/profil", name="user_profil", methods={"POST"} )
     */
    public function updateProfil(MailerInterface $mailer, Request $request, UserRepository $repo, EntityManagerInterface  $entityManager)
    {

        $data  = json_decode($request->getContent(), true);
        $user = $repo->find($data['id']);
        $email = $data['email'];
        $emails = $repo->findBy(['email' => $data['email']]);

        $temoins = false;
        foreach ($emails as $e) {
            if ($e->getEmail()  == $email && $e->getId() != $user->getId()) {
                $temoins = true;
            }
        }
        if ($temoins) {
            return new JsonResponse(["Email deja existant "], 500);
        }

        $user->setEmail($data['email']);
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setPhone($data['phone']);
        $user->setAdresse($data['adresse']);
        $user->setIsActiveNow($data['isActiveNow']);
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(["utilisateur mise a jour  avec succes "], 200);
    }

    /**
     * @Route("/api/users/update", name="user_update", methods={"POST"} )
     */
    public function update(MailerInterface $mailer, Request $request, UserRepository $repo, EntityManagerInterface  $entityManager)
    {
        $data  = json_decode($request->getContent(), true);

        $user = $repo->find($data['id']);
        $etat = $user->getEnabled();
        $status = $user->getStatus();

        $user->setFirstName($data['firstName']);
        // $user->setEmail($data['email']);
        $user->setEnabled($data['enabled']);
        $user->setLastName($data['lastName']);
        $user->setPhone($data['phone']);
        $user->setStatus($data['status']);
        $user->setRoles($data['roles']);

        if ($user->getEnabled() == false) {
            $user->setStatus("BLOCKE");
        }
        $entityManager->persist($user);
        $entityManager->flush();

        if ($user && $user->getEnabled() != $etat) {
            $email = (new TemplatedEmail())
                ->from('simlait@pdefs.com')
                ->to($user->getEmail())
                ->subject('Activation  Compte')
                ->htmlTemplate('main/mailActivationCompte.html.twig')
                ->context([
                    'user' => $user,
                ]);
            $mailer->send($email);
        }
        if ($user && $status == $user->getStatus()) {
            $email = (new TemplatedEmail())
                ->from('simlait@pdefs.com')
                ->to($user->getEmail())
                ->subject('Statut de votre Compte')
                ->htmlTemplate('main/mailStatutCompte.html.twig')
                ->context([
                    'user' => $user,
                ]);
            $mailer->send($email);
        }

        return new JsonResponse(["utilisateur mise a jour  avec succes "], 200);
    }

    /**
     * @Route("/api/usersConnected",name="users_connected", methods={"GET"})
     */
    public function userConnected(SerializerInterface $serializer, UserRepository $ur)
    {
        $user = $this->getUser();

        $users = $serializer->serialize($user, 'json');

        $response = new Response($users);

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }



    public function find_user_email(
        Request $request,
        UserMobileRepository $repo,
        LoggerInterface $logger,
        EntityManagerInterface $entityManager
    ): ?Response {

        $logger->info("requette", ['requette' => $request]);
        try {
            $data = json_decode($request->getContent(), true);
            $password = $data['password'];
            $email = $data['email'];
            $criteria = ['password' => $password, 'email' => $email];

            $user = $repo->findOneBy($criteria);
            $logger->info('User logged in', ['user' => $user]);
            if ($user ==  null) {
                $logger->critical("Email non valide", ['email' => $email]);
                throw  new  NotFoundHttpException(" user avec l'email $email non trouvé");
            }
            return new JsonResponse($user->asArray(), 200, ["Content-Type" => "application/json"]);
        } catch (\Exception $e) {
            return new JsonResponse(['err' => $e->getMessage()], 500);
        }
    }




    /**
     * @Route("/api/findUser/{email}", name="app_get_user",methods={"GET"})
     */
    public function getOneUserMobile(
        Request $request,
        UserMobileRepository $repo,
        LoggerInterface $logger,
        EntityManagerInterface $entityManager
    ): ?Response {

        $decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());
        $email = $decodedJwtToken['email'];

        try {
            // $user = $repo->findOneBy(['email'=> $content['email'] ]);
            $criteria = ['email' => $email];
            $user = $repo->findOneBy($criteria);
            if ($user ==  null) {
                $logger->critical("Email non valide", ['email' => $email]);
                throw  new  NotFoundHttpException(" user avec l'email $email non trouvé");
            }
            return new JsonResponse($user->asArray(), 200, ["Content-Type" => "application/json"]);
        } catch (\Exception $e) {
            $resultat =  ["RESULTAT", ['code' => 500, "err" => $e->getMessage()]];

            return new JsonResponse(['err' => $e->getMessage()], 500);
        }
    }

    /**
     * @Route("/api/users/updateAvatar",  name="app_upload_profil", methods={"POST"} )
     */
    public function uploadImage(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): ?Response
    {
        define('UPLOAD_DIR', 'avatars/');



        $datas = json_decode($request->getContent(), true);
        $url =  $datas['image'];
        $idUser = $datas['idUser'];

        $user = $userRepository->find($idUser);

        if (str_contains($url, ",")) {
            $po = strpos($url, ",");
            $avatar_base64 = substr($url,  $po + 1);
        }
        $data = base64_decode($avatar_base64);
        $file = UPLOAD_DIR . uniqid() . '.jpeg';
        $success = file_put_contents($file, $data);

        $type = pathinfo($file, PATHINFO_EXTENSION);
        $data = file_get_contents($file);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        try {

            $user->setAvatar($base64);

            $entityManager->persist($user);
            $entityManager->flush();

            return new JsonResponse("Avatar enregistrer avec succés !! ");
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage());
        }
    }
}