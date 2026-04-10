<?php

namespace App\Repository;

use App\Entity\Midwife;
use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Midwife|null find($id, $lockMode = null, $lockVersion = null)
 * @method Midwife|null findOneBy(array $criteria, array $orderBy = null)
 * @method Midwife[]    findAll()
 * @method Midwife[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
/** @extends ServiceEntityRepository<Midwife> */
class MidwifeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Midwife::class);
    }

    /** @return array<int, Midwife> */
    public function findByService(Service $service): array
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.services', 's')
            ->andWhere(':service = s')
            ->setParameter('service', $service)
            ->getQuery()
            ->getResult()
        ;
    }
    // /**
    //  * @return Midwife[] Returns an array of Midwife objects
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
    public function findOneBySomeField($value): ?Midwife
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
