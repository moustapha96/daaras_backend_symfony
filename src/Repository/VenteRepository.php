<?php

namespace App\Repository;

use App\Entity\Vente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method Vente|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vente|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vente[]    findAll()
 * @method Vente[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VenteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vente::class);
    }

    // /**
    //  * @return Collecte[] Returns an array of Collecte objects
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
    public function findOneBySomeField($value): ?Collecte
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findParCriteria(
        string $region,
        string $department,
        string $zone,
        string $produit,
        string $conditionnement,
        string $laiterie,
        string $emballage,
        string $dateDebut,
        string $dateFin
    ) {


        if ($dateDebut != null && $dateFin != null) {
            $dd = new \DateTime($dateDebut);
            $df = new \DateTime($dateFin);

            $qb = $this->createQueryBuilder('col');
            $qb->addSelect('condi')->join('col.conditionnements', 'condi')
                ->addSelect('lait')->join('col.unites', 'lait')
                ->addSelect('emb')->join('col.emballages', 'emb')
                ->addSelect('prod')->join('col.produits', 'prod')

                ->innerJoin('App\Entity\Region', 'reg', \Doctrine\ORM\Query\Expr\Join::WITH, 'lait.region = reg')
                ->innerJoin('App\Entity\Zones', 'zon', \Doctrine\ORM\Query\Expr\Join::WITH, 'lait.zone = zon')
                ->innerJoin('App\Entity\Departement', 'depart', \Doctrine\ORM\Query\Expr\Join::WITH, 'lait.departement = depart')


                ->where($qb->expr()->andX($qb->expr()->andX(
                    $qb->expr()->like('prod.nom', ':produit'),
                    $qb->expr()->like('condi.nom', ':conditionnement'),
                    $qb->expr()->like('lait.nom', ':laiterie'),
                    $qb->expr()->like('emb.nom', ':emballage'),
                    $qb->expr()->like('zon.nom', ':zone'),
                    $qb->expr()->like('depart.nom', ':departement'),
                    $qb->expr()->like('reg.nom', ':region'),
                    $qb->expr()->between('col.dateVente', ':dateDebut', ':dateFin'),

                )))
                ->setParameter('produit', '%' . $produit . '%')
                ->setParameter('conditionnement', '%' . $conditionnement . '%')
                ->setParameter('laiterie', '%' . $laiterie . '%')
                ->setParameter('zone', '%' . $zone . '%')
                ->setParameter('departement', '%' . $department . '%')
                ->setParameter('emballage', '%' . $emballage . '%')
                ->setParameter('region', '%' . $region . '%')
                ->setParameter('dateDebut', $dd)
                ->setParameter('dateFin', $df)
                ->orderBy('col.id', 'DESC');
            return $qb->getQuery()->getResult();
        } else {
            $qb = $this->createQueryBuilder('col');
            $qb->addSelect('condi')->join('col.conditionnements', 'condi')
                ->addSelect('lait')->join('col.unites', 'lait')
                ->addSelect('emb')->join('col.emballages', 'emb')
                ->addSelect('prod')->join('col.produits', 'prod')

                ->innerJoin('App\Entity\Region', 'reg', \Doctrine\ORM\Query\Expr\Join::WITH, 'lait.region = reg')
                ->innerJoin('App\Entity\Zones', 'zon', \Doctrine\ORM\Query\Expr\Join::WITH, 'lait.zone = zon')
                ->innerJoin('App\Entity\Departement', 'depart', \Doctrine\ORM\Query\Expr\Join::WITH, 'lait.departement = depart')


                ->where($qb->expr()->andX($qb->expr()->andX(
                    $qb->expr()->like('prod.nom', ':produit'),
                    $qb->expr()->like('condi.nom', ':conditionnement'),
                    $qb->expr()->like('lait.nom', ':laiterie'),
                    $qb->expr()->like('emb.nom', ':emballage'),
                    $qb->expr()->like('zon.nom', ':zone'),
                    $qb->expr()->like('depart.nom', ':departement'),
                    $qb->expr()->like('reg.nom', ':region'),
                )))

                ->setParameter('produit', '%' . $produit . '%')
                ->setParameter('conditionnement', '%' . $conditionnement . '%')
                ->setParameter('laiterie', '%' . $laiterie . '%')
                ->setParameter('zone', '%' . $zone . '%')
                ->setParameter('departement', '%' . $department . '%')
                ->setParameter('emballage', '%' . $emballage . '%')
                ->setParameter('region', '%' . $region . '%')

                ->orderBy('col.id', 'DESC');
            return $qb->getQuery()->getResult();
        }
    }


    public function findLaiterieWithDemande(string $zone, int $besoin, string $produit, string $dateDebut, string $dateFin)
    {
        $dd = new \DateTime($dateDebut);
        $df = new \DateTime($dateFin);

        $qb = $this->createQueryBuilder('col');
        $qb->addSelect('condi')->join('col.conditionnements', 'condi')
            ->addSelect('emb')->join('col.emballages', 'emb')
            ->addSelect('prod')->join('col.produits', 'prod')
            ->addSelect('lait')->join('col.unites', 'lait')

            ->innerJoin('App\Entity\Region', 'reg', \Doctrine\ORM\Query\Expr\Join::WITH, 'lait.region = reg')
            ->innerJoin('App\Entity\Zones', 'zon', \Doctrine\ORM\Query\Expr\Join::WITH, 'lait.zone = zon')
            ->innerJoin('App\Entity\Departement', 'depart', \Doctrine\ORM\Query\Expr\Join::WITH, 'lait.departement = depart')


            ->addSelect('col.quantite as quantite')
            ->addSelect('emb.nom as emballage')
            ->addSelect('condi.nom as conditionnement')
            ->addSelect('lait.nom as laiterie')
            ->addSelect('lait.id as idLaiterie')
            ->addSelect('reg.nom as region')
            ->addSelect('depart.nom as departement')
            ->addSelect('zon.nom as zone')
            ->addSelect('lait.telephone as telephone')
            ->addSelect('lait.adresse as adresse')
            ->addSelect('col.prix as prix')
            ->addSelect('prod.nom as produit')

            ->where($qb->expr()->andX($qb->expr()->andX(
                $qb->expr()->between('col.dateVente', ':dateDebut', ':dateFin'),
            )))
            ->andWhere('col.quantite >= :besoin')
            ->andWhere('prod.nom = :produit')

            ->andWhere('zon.nom = :zone')

            ->setParameter('zone', $zone)
            ->setParameter('besoin',  $besoin)
            ->setParameter('produit', $produit)
            ->setParameter('dateDebut', $dd)
            ->setParameter('dateFin', $df)

            ->orderBy('col.id', 'DESC');
        return $qb->getQuery()->getResult();
    }
}
