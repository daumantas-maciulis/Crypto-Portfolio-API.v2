<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\AssetModel;
use App\Entity\Asset;
use App\Form\AssetType;
use App\Serializer\Serializer;
use App\Service\UpdateCryptoPricesInUsdService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * @Route("/api/v1/asset")
 */
class AssetController extends AbstractController
{
    /**
     * @Route("", methods="POST")
     */
    public function addNewAssetAction(Request $request, AssetModel $assetModel, UpdateCryptoPricesInUsdService $service, Serializer $serializer): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $deserializedDataFromRequest = $serializer->deserialize($request->getContent(), Asset::class, 'json');
        $form = $this->createForm(AssetType::class, $deserializedDataFromRequest);
        $form->submit(json_decode($request->getContent(), true));

        if ($form->isSubmitted() && $form->isValid()) {
            $createdAsset = $assetModel->addNewAsset($form->getData(), $this->getUser());
            $service->updateCryptoPricesInUsd($this->getUser());
            return $this->responseJson($createdAsset, Response::HTTP_CREATED);
        }
        $errorResponse = $this->getErrorsFromForm($form);

        return $this->responseJson($errorResponse, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("", methods="GET")
     */
    public function getAllAssetsAction(AssetModel $assetModel, UpdateCryptoPricesInUsdService $service): JsonResponse
    {
        $assets = $assetModel->getAllAssets($this->getUser());
        if ($assets === null) {
            $response = [
                'error' => 'You have no assets'
            ];
            return $this->responseJson($response, Response::HTTP_OK);
        }
        $service->updateCryptoPricesInUsd($this->getUser());
        return $this->responseJson($assets, Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", methods="GET")
     */
    public function getOneAssetAction($id, AssetModel $assetModel, UpdateCryptoPricesInUsdService $service): JsonResponse
    {
        $asset = $assetModel->getOneAsset($id, $this->getUser());
        if ($asset === null) {
            $message = sprintf('Asset No. %s is non existent or belongs not to you', $id);
            $response = [
                'error' => $message
            ];
            return $this->responseJson($response, Response::HTTP_OK);
        }
        $service->updateCryptoPricesInUsd($this->getUser());
        return $this->responseJson($asset, Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", methods="DELETE")
     */
    public function deleteAssetAction($id, AssetModel $assetModel): JsonResponse
    {
        $assetDeleted = $assetModel->deleteAsset($id, $this->getUser());

        if (!$assetDeleted) {
            $message = sprintf("Asset with No. %s is non existent or it does not belong to you", $id);
            $response = [
                'error' => $message
            ];
            return $this->responseJson($response, Response::HTTP_BAD_REQUEST);
        }

        $message = sprintf('Asset No. %s was successfully deleted', $id);
        $response = [
            'success' => $message
        ];

        return $this->responseJson($response, Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", methods="PATCH")
     */
    public function patchAssetAction($id, AssetModel $assetModel, Request $request, UpdateCryptoPricesInUsdService $service, Serializer $serializer): JsonResponse
    {
        $deserializedDataFromRequest = $serializer->deserialize($request->getContent(), Asset::class, 'json');
        $form = $this->createForm(AssetType::class, $deserializedDataFromRequest);

        $form->submit(json_decode($request->getContent(), true));

        if ($form->isSubmitted() && $form->isValid()) {
            $patchedAsset = $assetModel->updateAsset($form->getData(), $id, $this->getUser());
            if (!$patchedAsset) {
                $message = sprintf("Asset with No. %s is non existent or it does not belong to you", $id);
                $response = [
                    'error' => $message
                ];
                return $this->responseJson($response, Response::HTTP_BAD_REQUEST);
            }
            $service->updateCryptoPricesInUsd($this->getUser());
            return $this->responseJson($patchedAsset, Response::HTTP_CREATED);
        }
        $errorResponse = $this->getErrorsFromForm($form);

        return $this->responseJson($errorResponse, Response::HTTP_BAD_REQUEST);
    }


    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = [];

        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }
        $errorResponse = [
            'error' => $errors
        ];
        return $errorResponse;
    }

    private function responseJson(Asset|array $responseMessage, int $responseCode): JsonResponse
    {
        return $this->json($responseMessage, $responseCode, [], [
            ObjectNormalizer::IGNORED_ATTRIBUTES => ['owner']
        ]);
    }
}

