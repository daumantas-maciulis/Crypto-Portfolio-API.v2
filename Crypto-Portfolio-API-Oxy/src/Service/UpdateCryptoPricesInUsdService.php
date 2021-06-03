<?php


namespace App\Service;


use App\Client\CoinLayer\Client;
use App\Entity\Asset;
use App\Model\AssetModel;
use App\Model\CryptoPricesModel;
use Symfony\Component\Security\Core\User\UserInterface;

class UpdateCryptoPricesInUsdService
{
    private AssetModel $assetModel;
    private Client $client;
    private CryptoPricesModel $cryptoPricesModel;

    public function __construct(AssetModel $assetModel, Client $client, CryptoPricesModel $cryptoPricesModel)
    {
        $this->assetModel = $assetModel;
        $this->client = $client;
        $this->cryptoPricesModel = $cryptoPricesModel;
    }

    public function updateCryptoPricesInUsd(UserInterface $user)
    {
        $userAssets = $this->assetModel->getAllAssets($user);
        /** @var Asset $asset */
        foreach ($userAssets as $asset) {
           $currencyType = $asset->getCurrency();
           if($currencyType === 'IOTA') {
               $currencyType = 'MIOTA';
           }
           $currencyValue = $asset->getValue();
           $currencyValueInUsd = $this->cryptoPricesModel->getCryptoPriceByCurrency($currencyType);

           $calculatedCryptoPriceInUsd = $currencyValue * $currencyValueInUsd;

           $this->assetModel->updateAssetPriceInUsd($asset, $calculatedCryptoPriceInUsd);
        }
    }
}