<?php

namespace App\Repository;

use App\Entity\Book;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
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

    public function searchByRef($id)
{
    return $this->createQueryBuilder('b')
        ->andWhere('b.id = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getResult();
}

public function triByAuthor()
{
    return $this->createQueryBuilder('b')
        ->leftJoin('b.author', 'a')
        ->orderBy('a.username', 'ASC')
        ->getQuery()
        ->getResult();
}

public function searchBook()
{
    return $this->createQueryBuilder('b')
        ->leftJoin('b.author', 'a')
        ->Where('b.publicationDate < :Date')
        ->andWhere('a.nb_books > :nbr')
        ->setParameter('Date',new \DateTime('2023-01-01'))
        ->setParameter('nbr',35)
        ->getQuery()
        ->getResult();
}

public function getpub(){
    $EntityManager=$this->getEntityManager();
    $query = $EntityManager->createQuery('SELECT COUNT(b.id) FROM App\Entity\Book b WHERE b.published LIKE :value')
    ->setParameter('value', 'Yes%');
    return $query->getResult();
}
public function getpubno(){
    $EntityManager=$this->getEntityManager();
    $query = $EntityManager->createQuery('SELECT COUNT(b.id) FROM App\Entity\Book b WHERE b.published LIKE :value')
    ->setParameter('value', 'No%');
    return $query->getResult();
}
public function getnb_cat_sc(){
    $EntityManager=$this->getEntityManager();
    $query = $EntityManager->createQuery('SELECT COUNT(b.id) FROM App\Entity\Book b WHERE b.category LIKE :value')
    ->setParameter('value', 'Science Fiction%');
    return $query->getResult();
}

public function getbookwithdate()
{
    $entityManager = $this->getEntityManager();

    $query = $entityManager->createQuery('SELECT b FROM App\Entity\Book b WHERE b.publicationDate BETWEEN :startDate AND :endDate')
        ->setParameter('startDate', new \DateTime('2014-01-01'))
        ->setParameter('endDate', new \DateTime('2018-12-31'));

    return $query->getResult();
}


//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
