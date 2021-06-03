<?php
declare(strict_types=1);

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

    private function saveData(CryptoPrices $cryptoPrices): void
    {
        $this->entityManager->persist($cryptoPrices);
        $this->entityManager->flush();
    }

    public function saveCryptoPrices(array $cryptoPricesArray): void
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

    public function getCryptoPriceByCurrency(string $currency): float
    {
        $cryptoCurrency = $this->entityManager->getRepository(CryptoPrices::class)->findOneBy(['name' => $currency]);
        /** @var CryptoPrices $cryptoCurrency */
        return $cryptoCurrency->getValue();
    }
}