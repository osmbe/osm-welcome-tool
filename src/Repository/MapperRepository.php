<?php

namespace App\Repository;

use App\Entity\Mapper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Mapper|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mapper|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mapper[]    findAll()
 * @method Mapper[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MapperRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mapper::class);
    }

    // /**
    //  * @return Mapper[] Returns an array of Mapper objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Mapper
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
