<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\service\ResetPassworService;
use Doctrine\DBAL\Schema\View;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;


class MyResetPassWordController extends AbstractController
{


    private $resetPasswordService;
    /**
     * @param ResetPassworService $resetPasswordService
     */
    public function __construct(ResetPassworService $resetPasswordService)
    {
        $this->resetPasswordService = $resetPasswordService;
    }

    /**
     * @Route("/api/reset-password", name="api_reset_password_url" , methods={"POST"} )
     * @param Request $request
     */
    public function resetPassword(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'];
        $uri = $data['uri'];

        $this->resetPasswordService->processSendingPasswordResetEmail($email, $uri);

        return new JsonResponse("Url de reinistialisation de mot de passe envoyer", 200);
    }


    /**
     * @Route("/api/reset-password/new-password", name="api_reset_password_new" , methods={"POST"} )
     * @param Request $request
     */
    public function newPassword(Request $request): Response
    {

        $data = json_decode($request->getContent(), true);

        $password = $data['password'];
        $token = $data['token'];

        $this->resetPasswordService->newPassword($password, $token);

        return new JsonResponse("Mot de passe reinitialiser", 200);
    }
}