<?php


namespace App\Model;


use App\Entity\CryptoPrices;
use Doctrine\ORM\EntityManagerInterface;

class CryptoPricesModel
{
    private const BTC = 'BTC';
    private const ETH = 'ETH';
    private const IOTA = 'IOTA';

    private const CRYPT_CURRENCIES = [
        self::BTC,
        self::ETH,
        self::IOTA
    ];

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    private function saveData(CryptoPrices $cryptoPrices)
    {
        $this->entityManager->persist($cryptoPrices);
        $this->entityManager->flush();
    }

    public function saveOrUpdateCryptoPrices(array $cryptoPricesArray)
    {
        foreach(self::CRYPT_CURRENCIES as $cryptoCurrency) {
            if($cryptoCurrency === 'IOTA')
            {
                $cryptoCurrency = 'MIOTA';
            };

            $cryptoPrice = $this->entityManager->getRepository(CryptoPrices::class)->findOneBy(['name' => $cryptoCurrency]);
            if(!$cryptoPrice) {
                $cryptoPrice = new CryptoPrices();
                $cryptoPrice->setName($cryptoCurrency);
            }
            $cryptoPrice->setValue($cryptoPricesArray[$cryptoCurrency]);

            $this->saveData($cryptoPrice);
        }
    }
}