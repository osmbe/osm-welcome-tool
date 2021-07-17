<?php

namespace App\Repository;

use App\Entity\Welcome;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Welcome|null find($id, $lockMode = null, $lockVersion = null)
 * @method Welcome|null findOneBy(array $criteria, array $orderBy = null)
 * @method Welcome[]    findAll()
 * @method Welcome[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WelcomeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Welcome::class);
    }

    // /**
    //  * @return Welcome[] Returns an array of Welcome objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Welcome
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
