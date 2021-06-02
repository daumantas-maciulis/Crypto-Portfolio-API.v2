<?php


namespace App\Client\CoinLayer;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class Client extends AbstractController
{
    private const BASE_URI = 'http://api.coinlayer.com';
    private const ENDPOINT = '/live';
    private const TARGET_CURRENCY = "target=USD";

    private const CRYPTO_CURRENCIES = "symbols=BTC,ETH,MIOTA";


    public function getCryptoPricesInUsd()
    {
        $api_key = $this->getParameter('app.api_key');

        $client = new \GuzzleHttp\Client([
            'base_uri' => self::BASE_URI
        ]);

        $requestURL = sprintf('%s?access_key=%s&%s&%s', self::ENDPOINT, $api_key, self::TARGET_CURRENCY, self::CRYPTO_CURRENCIES);

        $apiResponse = $client->request(Request::METHOD_GET, $requestURL);

        $apiResponseArray = json_decode($apiResponse->getBody()->getContents(), true);


        return $apiResponseArray['rates'];
    }

}