<?php

namespace App\Repository;

use App\Entity\CryptoPrices;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CryptoPrices|null find($id, $lockMode = null, $lockVersion = null)
 * @method CryptoPrices|null findOneBy(array $criteria, array $orderBy = null)
 * @method CryptoPrices[]    findAll()
 * @method CryptoPrices[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CryptoPricesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CryptoPrices::class);
    }

    // /**
    //  * @return CryptoPrices[] Returns an array of CryptoPrices objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CryptoPrices
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
