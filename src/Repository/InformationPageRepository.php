<?php

namespace App\Repository;

use App\Entity\InformationPage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InformationPage|null find($id, $lockMode = null, $lockVersion = null)
 * @method InformationPage|null findOneBy(array $criteria, array $orderBy = null)
 * @method InformationPage[]    findAll()
 * @method InformationPage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
/** @extends ServiceEntityRepository<InformationPage> */
class InformationPageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InformationPage::class);
    }

    // /**
    //  * @return InformationPage[] Returns an array of InformationPage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InformationPage
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
