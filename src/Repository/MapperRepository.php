<?php

namespace App\Repository;

use App\Entity\Changeset;
use App\Entity\Mapper;
use App\Entity\Region;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Mapper|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mapper|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mapper[]    findAll()
 * @method Mapper[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Mapper>
 */
class MapperRepository extends ServiceEntityRepository
{
    public const MAPPERS_PER_PAGE = 50;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mapper::class);
    }

    public function findPaginated(Region $region, int $year, int $month, int $page): Paginator
    {
        $from = new \DateTimeImmutable(\sprintf('%d-%02d-01 00:00:00', $year, $month));
        $to = (clone $from)->modify('last day of this month')->setTime(23, 59, 59);

        $page = max(1, $page);

        $query = $this->createQueryBuilder('m')
            ->addSelect('(SELECT MIN(c.created_at) FROM '.Changeset::class.' c WHERE c.mapper = m.id) AS HIDDEN firstChangeset')
            // Filter on region
            ->join('m.region', 'r')
            ->andWhere('r.id = :region')
            ->setParameter('region', $region->getId())
            // Filter on date range
            ->andWhere('(SELECT MIN(c1.created_at) FROM '.Changeset::class.' c1 WHERE c1.mapper = m.id) >= :from')
            ->setParameter('from', $from)
            ->andWhere('(SELECT MIN(c2.created_at) FROM '.Changeset::class.' c2 WHERE c2.mapper = m.id) <= :to')
            ->setParameter('to', $to)
            // Set order by first changeset date
            ->orderBy('firstChangeset', 'DESC')
            // Set pagination
            ->setFirstResult(($page - 1) * self::MAPPERS_PER_PAGE)
            ->setMaxResults(self::MAPPERS_PER_PAGE)
            ->getQuery()
        ;

        // dd($query->getSQL());

        return new Paginator($query, true);
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
