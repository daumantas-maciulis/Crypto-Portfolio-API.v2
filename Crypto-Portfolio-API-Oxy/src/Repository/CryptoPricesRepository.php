<?php
declare(strict_types=1);

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
}

