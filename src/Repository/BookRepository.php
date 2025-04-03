<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * Find all books ordered by name.
     *
     * @return Book[]
     */
    public function findAllOrderedByTitle(): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.title', 'ASC')
            ->getQuery()
            ->getResult();
    }
}