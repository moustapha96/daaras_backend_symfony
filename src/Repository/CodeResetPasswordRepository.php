<?php

namespace App\Repository;

use App\Entity\CodeResetPassword;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CodeResetPassword|null find($id, $lockMode = null, $lockVersion = null)
 * @method CodeResetPassword|null findOneBy(array $criteria, array $orderBy = null)
 * @method CodeResetPassword[]    findAll()
 * @method CodeResetPassword[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CodeResetPasswordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CodeResetPassword::class);
    }

    // /**
    //  * @return CodeResetPassword[] Returns an array of CodeResetPassword objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CodeResetPassword
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
