<?php


namespace App\Controller;


use App\Client\CoinLayer\Client as CryptoPricesClient;
use App\Model\CryptoPricesModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CryptoCurrencyController extends AbstractController
{
    private CryptoPricesClient $cryptoPricesClient;
    private CryptoPricesModel $cryptoModel;

    public function __construct(CryptoPricesClient $cryptoPricesClient, CryptoPricesModel $cryptoModel)
    {
        $this->cryptoPricesClient = $cryptoPricesClient;
        $this->cryptoModel = $cryptoModel;
    }

    public function saveCryptoPricesAction(): void
    {
        $cryptoPricesArray = $this->cryptoPricesClient->getCryptoPricesInUsd();
        $this->cryptoModel->saveCryptoPrices($cryptoPricesArray);
    }
}