<?php

namespace App\Repository;

use App\Entity\MediaFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MediaFile>
 */
class MediaFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MediaFile::class);
    }

    public function findByFilename(string $filename): ?MediaFile
    {
        return $this->findOneBy(['filename' => $filename]);
    }

    /** @return list<MediaFile> */
    public function search(string $term): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.filename LIKE :term OR m.title LIKE :term OR m.alt LIKE :term')
            ->setParameter('term', '%'.$term.'%')
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
