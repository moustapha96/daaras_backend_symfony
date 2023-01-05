<?php

namespace App\Repository;

use App\Entity\UserMobile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserMobile|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserMobile|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserMobile[]    findAll()
 * @method UserMobile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserMobileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMobile::class);
    }

    // /**
    //  * @return UserMobile[] Returns an array of UserMobile objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserMobile
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
   


    public function findLaiterieUser(string $email){
        $qb = $this->createQueryBuilder('user');

        $qb->addSelect('lait')->join('user.unites', 'lait')
            ->addSelect('lait as laiteries')
            ->where('user.email = :email')
            ->setParameter('email', $email)
            ->orderBy('lait.id', 'DESC');
        return $qb->getQuery()->getResult();
    }
}
