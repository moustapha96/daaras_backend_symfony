<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;


final class UserDataPersister implements ContextAwareDataPersisterInterface
{
    private $decorated;
    private $userPasswordHasher;
    private $tokenStorage;

    public function __construct(
        \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface  $tokenStorage,
        ContextAwareDataPersisterInterface $decorated,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher
    ) {
        $this->decorated = $decorated;
        $this->userPasswordHasher =  $userPasswordHasher;
        $this->entityManager =  $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function supports($data, array $context = []): bool
    {
        return $this->decorated->supports($data, $context);
    }

    public function persist($data, array $context = [])
    {
        if (($context['collection_operation_name'] ?? null) === 'post' ||
            ($context['graphql_operation_name'] ?? null) === 'create'
        ) {
            if ($data instanceof UserInterface &&  $data instanceof User  &&  $data->getPlainPassword() !== null && $data instanceof User) {
                $data->setPassword($this->userPasswordHasher->hashPassword($data, $data->getPlainPassword()));
                $data->setPlainPassword("");
            }
            if (method_exists($data, 'setOwner') &&  $this->tokenStorage->getToken() != null &&  is_object($user = $this->tokenStorage->getToken()->getUser())) {
                $data->setOwner($user);
            }
            if (method_exists($data, 'setCreatedAt')) {
                $data->setCreatedAt(new \DateTimeImmutable());
            }
        }
        return  $this->decorated->persist($data, $context);
    }

    public function remove($data, array $context = [])
    {
        return $this->decorated->remove($data, $context);
    }
}
