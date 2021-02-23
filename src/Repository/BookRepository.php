<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @return Book[] Returns an array of Book objects
     */
    
    public function findByList(Array $value)
    {
        $query = $this->createQueryBuilder('b');

        if ($value['name'])
        {
            $query->andWhere('b.name LIKE :name')
                  ->setParameter('name', '%' . $value['name'] . '%');
        }

        if ($value['date'])
        {
            $query->andWhere('b.date > :date')
                    ->setParameter('date', $value['date']);
        }

        if ($value['author'])
        {
            $query->andWhere('b.author = :author')
                    ->setParameter('author', $value['author']);
        }

        if ($value['categories'])
        {
            $query->andWhere('b.category = :category')
                    ->setParameter('category', $value['categories']);
        }

        return$query->getQuery()
                    ->getResult();
    }
    

    /*
    public function findOneBySomeField($value): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
