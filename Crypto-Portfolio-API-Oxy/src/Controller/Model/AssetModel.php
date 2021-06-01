<?php


namespace App\Controller\Model;


use App\Entity\Asset;
use Doctrine\ORM\EntityManagerInterface;

class AssetModel
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    private function saveData(Asset $asset): Asset
    {
        $this->entityManager->persist($asset);
        $this->entityManager->flush();

        return $asset;
    }

    public function updateAsset(Asset $assetFromForm, int $id): ?Asset
    {
        /** @var Asset $assetFromDb */
        $assetFromDb = $this->entityManager->getRepository(Asset::class)->find($id);
        if(!$assetFromDb)
        {
            return null;
        }

        $assetFromDb->setLabel($assetFromForm->getLabel());
        $assetFromDb->setCurrency($assetFromForm->getCurrency());
        $assetFromDb->setValue($assetFromForm->getValue());

        return $this->saveData($assetFromDb);
    }

    private function deleteData(Asset $asset): void
    {
        $this->entityManager->remove($asset);
        $this->entityManager->flush();
    }

    public function addNewAsset(Asset $asset): Asset
    {
        return $this->saveData($asset);
    }

    public function getAllAssets(): array
    {
        return $this->entityManager->getRepository(Asset::class)->findAll();
    }

    public function getOneAsset(int $id): Asset
    {
        /** @var Asset $asset */
        $asset = $this->entityManager->getRepository(Asset::class)->find($id);

        return $asset;
    }

    public function deleteAsset(int $id): bool
    {
        $asset = $this->entityManager->getRepository(Asset::class)->find($id);

        if (!$asset) {
            return false;
        }

        return true;
    }
}