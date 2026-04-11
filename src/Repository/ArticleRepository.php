<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
/** @extends ServiceEntityRepository<Article> */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findPublishedQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isPublished = :published')
            ->andWhere('a.publishedAt <= :now OR a.publishedAt IS NULL')
            ->setParameter('published', true)
            ->setParameter('now', new \DateTime())
            ->orderBy('a.publishedAt', 'DESC')
            ->addOrderBy('a.createdAt', 'DESC');
    }

    public function search(?string $keyword = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.author', 'm')
            ->orderBy('a.createdAt', 'DESC');

        if ($keyword) {
            $qb->andWhere('a.title LIKE :keyword OR m.lastname LIKE :keyword OR m.firstname LIKE :keyword')
                ->setParameter('keyword', '%'.$keyword.'%');
        }

        return $qb;
    }
}
