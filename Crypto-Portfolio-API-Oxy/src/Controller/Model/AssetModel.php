<?php


namespace App\Controller\Model;


use App\Entity\Asset;
use App\Repository\AssetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

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

    public function addNewAsset(Asset $asset): Asset
    {
        return $this->saveData($asset);
    }

    public function getAllAssets(): array
    {
        return $this->entityManager->getRepository(Asset::class)->findAll();
    }
}