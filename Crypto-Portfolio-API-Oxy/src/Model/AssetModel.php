<?php
declare(strict_types=1);

namespace App\Model;


use App\Entity\Asset;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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

    public function updateAsset(Asset $assetFromForm, int $id, UserInterface $user): ?Asset
    {
        /** @var Asset $assetFromDb */
        $assetFromDb = $this->entityManager->getRepository(Asset::class)->findOneBy(['id' => $id, 'owner' => $user]);
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

    public function addNewAsset(Asset $asset, UserInterface $user): Asset
    {
        /** @var User $user */
        $asset->setOwner($user);
        return $this->saveData($asset);
    }

    public function getAllAssets(UserInterface $user): ?array
    {
        /** @var User $user */
        $assets = $this->entityManager->getRepository(Asset::class)->findBy(['owner' => $user]);
        if(!$assets) {
            return null;
        }

        return $assets;
    }

    public function getOneAsset(string $id, UserInterface $user): ?Asset
    {
        /** @var Asset $asset */
        $asset = $this->entityManager->getRepository(Asset::class)->findOneBy(['id' => $id, 'owner' => $user]);
        if(!$asset) {
            return null;
        }

        return $asset;
    }

    public function deleteAsset(string $id, UserInterface $user): bool
    {
        $asset = $this->entityManager->getRepository(Asset::class)->findOneBy(['id' => $id, 'owner' => $user]);

        if (!$asset) {
            return false;
        }
        /** @var Asset $asset */
        $this->deleteData($asset);
        return true;
    }
}