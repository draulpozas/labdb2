<?php

namespace App\Repository;

use App\Entity\Reagent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Reagent|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reagent|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reagent[]    findAll()
 * @method Reagent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReagentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reagent::class);
    }

    // /**
    //  * @return Reagent[] Returns an array of Reagent objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Reagent
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
