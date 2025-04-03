<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Find all articles ordered by name.
     *
     * @return Article[]
     */
    public function findAllOrderedByTitle(): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.title', 'ASC')
            ->getQuery()
            ->getResult();
    }
}