<?php


namespace App\Controller;


use App\Controller\Model\AssetModel;
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
            $errors = $this->getErrorsFromForm($form);
            $errorResponse = [
                'error' => $errors
            ];
            return $this->json($errorResponse, Response::HTTP_BAD_REQUEST);
        }

        if ($form->isSubmitted()) {
            $createdAsset = $assetModel->addNewAsset($form->getData());

            return $this->json($createdAsset, Response::HTTP_CREATED, [], [
                ObjectNormalizer::IGNORED_ATTRIBUTES => []
            ]);
        }
    }

    /**
     * @Route("", methods="GET")
     */
    public function getAllAssetsAction(AssetModel $assetModel): JsonResponse
    {
        $assets = $assetModel->getAllAssets();

        return $this->json($assets, Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", methods="GET")
     */
    public function getOneAssetAction($id, AssetModel $assetModel): JsonResponse
    {
        $asset = $assetModel->getOneAsset($id);
        return $this->json($asset, Response::HTTP_OK, [], [
            ObjectNormalizer::IGNORED_ATTRIBUTES => []
        ]);
    }

    /**
     * @Route("/{id}", methods="DELETE")
     */
    public function deleteAssetAction($id, AssetModel $assetModel): JsonResponse
    {
        $assetDeleted = $assetModel->deleteAsset($id);

        if(!$assetDeleted)
        {
            $response = sprintf("Asset with No. %s is non existent or it does not belong to you", $id);
            return $this->json($response, Response::HTTP_BAD_REQUEST);
        }

        $response = sprintf('Asset No. %s was successfully deleted', $id);

        return $this->json($response, Response::HTTP_OK);

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
            $errors = $this->getErrorsFromForm($form);
            $errorResponse = [
                'error' => $errors
            ];
            return $this->json($errorResponse, Response::HTTP_BAD_REQUEST);
        }

        if ($form->isSubmitted()) {
            $patchedAsset = $assetModel->updateAsset($form->getData(), $id);

            return $this->json($patchedAsset, Response::HTTP_CREATED, [], [
                ObjectNormalizer::IGNORED_ATTRIBUTES => []
            ]);
        }
    }


    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = array();
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
        return $errors;
    }
}

