<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\AssetModel;
use App\Entity\Asset;
use App\Form\AssetType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/v1/asset")
 */
class AssetController extends AbstractController
{
    /**
     * @Route("", methods="POST")
     */
    public function addNewAssetAction(Request $request, AssetModel $assetModel, ValidatorInterface $validator): JsonResponse
    {

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        $deserializedDataFromRequest = $serializer->deserialize($request->getContent(), Asset::class, 'json');
        $form = $this->createForm(AssetType::class, $deserializedDataFromRequest);
        $form->submit(json_decode($request->getContent(), true));
        if (!$form->isValid()) {
            $errorResponse = $this->getErrorsFromForm($form);

            return $this->responseJson($errorResponse, Response::HTTP_BAD_REQUEST);
        }

        if ($form->isSubmitted()) {
            $createdAsset = $assetModel->addNewAsset($form->getData(), $this->getUser());

            return $this->responseJson($createdAsset, Response::HTTP_CREATED);
        }
    }

    /**
     * @Route("", methods="GET")
     */
    public function getAllAssetsAction(AssetModel $assetModel, Request $request): JsonResponse
    {
        $assets = $assetModel->getAllAssets($this->getUser());
        if ($assets === null) {
            $response = [
                'error' => 'You have no assets'
            ];
            return $this->responseJson($response, Response::HTTP_OK);
        }

        return $this->responseJson($assets, Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", methods="GET")
     */
    public function getOneAssetAction($id, AssetModel $assetModel): JsonResponse
    {
        $asset = $assetModel->getOneAsset($id, $this->getUser());
        if ($asset === null) {
            $message = sprintf('Asset No. %s is non existent or belongs not to you', $id);
            $response = [
                'error' => $message
            ];
            return $this->responseJson($response, Response::HTTP_OK);
        }
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
    public function patchAssetAction($id, AssetModel $assetModel, Request $request): JsonResponse
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        $deserializedDataFromRequest = $serializer->deserialize($request->getContent(), Asset::class, 'json');
        $form = $this->createForm(AssetType::class, $deserializedDataFromRequest);

        $form->submit(json_decode($request->getContent(), true));
        if (!$form->isValid()) {
            $errorResponse = $this->getErrorsFromForm($form);

            return $this->responseJson($errorResponse, Response::HTTP_BAD_REQUEST);
        }

        if ($form->isSubmitted()) {
            $patchedAsset = $assetModel->updateAsset($form->getData(), $id, $this->getUser());
            if (!$patchedAsset) {
                $message = sprintf("Asset with No. %s is non existent or it does not belong to you", $id);
                $response = [
                    'error' => $message
                ];
                return $this->responseJson($response, Response::HTTP_BAD_REQUEST);
            }

            return $this->responseJson($patchedAsset, Response::HTTP_CREATED);
        }
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

