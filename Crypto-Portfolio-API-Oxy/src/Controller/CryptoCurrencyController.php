<?php


namespace App\Controller;


use App\Client\CoinLayer\Client as CryptoPricesClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CryptoCurrencyController extends AbstractController
{
    public function saveCryptoPricesAction(CryptoPricesClient $cryptoPricesClient, CryptoModel $cryptoModel)
    {
        //request prices
        $cryptoPricesArray = $cryptoPricesClient->getCryptoPricesInUsd();
        //save them into database
        $cryptoModel->saveCryptoPrices($cryptoPricesArray);
    }
}