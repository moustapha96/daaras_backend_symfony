<?php

namespace App\service;

use App\Entity\Emprunt;
use App\Entity\ResetPasswordRequest;
use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class ResetPassworService extends AbstractController
{


    use ResetPasswordControllerTrait;

    private $resetPasswordHelper;
    private $entityManager;
    private $tokenGenerator;
    private $mailer;
    private $translator;
    private $userPasswordHasher;
    private $userRepository;
    public function __construct(
        ResetPasswordHelperInterface $resetPasswordHelper,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        TranslatorInterface $translator,
        UserPasswordHasherInterface $userPasswordHasher,
        UserRepository $userRepository
        // TokenGeneratorInterface $tokenGenerator
    ) {
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * @param string $password
     * @param string $token
     * @throws EntityNotFoundException
     */
    public function newPassword(string $password, string $token)
    {

        if (!$token) {
            throw new EntityNotFoundException("token invalide");
        }
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['reset_token' => $token]);

        if (!$user) {
            throw new EntityNotFoundException("utilisateur avec ce token " . $token . " n'esxite pas ");
        }
        $user->setResetToken(null);
        $encodedPassword = $this->userPasswordHasher->hashPassword(
            $user,
            $password
        );
        $user->setPassword($encodedPassword);
        $this->entityManager->flush();
    }


    /**
     * @param string $password
     * @param string $token
     * @throws EntityNotFoundException
     */
    public function reset(string $password, string $token = null)
    {
        if ($token) {
            $this->storeTokenInSession($token);
        }

        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw new  EntityNotFoundException("Aucun jeton de réinitialisation du mot de passe trouvé dans l'URL ou dans la session.");
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $error =  sprintf(
                '%s - %s',
                $this->translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE, [], 'ResetPasswordBundle'),
                $this->translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            );
            throw new EntityNotFoundException($error);
        }

        $this->resetPasswordHelper->removeResetRequest($token);

        $encodedPassword = $this->userPasswordHasher->hashPassword(
            $user,
            $password
        );

        $user->setPassword($encodedPassword);
        $this->entityManager->flush();

        // The session is cleaned up after the password has been changed.
        $this->cleanSessionAfterReset();
    }


    /**
     * @param string $emailFormData
     * @param string $uri
     * @throws EntityNotFoundException
     */
    public function processSendingPasswordResetEmail(string $emailFormData, string $uri)
    {
        $user = $this->userRepository->findOneBy(["email" => $emailFormData]);

        if (!$user) {
            throw new EntityNotFoundException("Cette adresse e-mail est inconnue");
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
            $time = $this->resetPasswordHelper->getTokenLifetime();
            $user->setResetToken($resetToken->getToken());
        } catch (ResetPasswordExceptionInterface $e) {
            $error = "Veuillez vérifier votre boite mail ou réessayer bientôt";
            return new JsonResponse(['error => ' . $error, 200]);
            throw new EntityNotFoundException($e);
        }

        $url = $uri . $resetToken->getToken();
        $email = (new TemplatedEmail())
            ->from(new Address('daaras@mas.sn', 'DAARAS SIMLAIT'))
            ->to($user->getEmail())
            ->subject('Votre demande de réinitialisation de mot de passe')
            ->htmlTemplate('reset_password/emailTokenApi.html.twig')
            ->context([
                'url' => $url,
                'resetToken' => $resetToken,
                'time' => $time
            ]);
        $this->mailer->send($email);
        // $this->setTokenObjectInSession($resetToken);
        return new JsonResponse(['success' => true]);
    }
}