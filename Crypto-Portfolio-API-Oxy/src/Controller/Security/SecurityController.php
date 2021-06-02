<?php
declare(strict_types=1);

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\UserType;
use App\Model\UserModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/api/v1")
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/create-account", methods="POST")
     */
    public function createNewAccountAction(Request $request, UserModel $userModel)
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        $deserializedDataFromRequest = $serializer->deserialize($request->getContent(), User::class, 'json');

        $form = $this->createForm(UserType::class, $deserializedDataFromRequest);

        $form->submit(json_decode($request->getContent(), true));
        if (!$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);
            $errorResponse = [
                'error' => $errors
            ];
            return $this->json($errorResponse, Response::HTTP_BAD_REQUEST);
        }

        if ($form->isSubmitted()) {
            $token = $userModel->addNewUser($form->getData());
            $responseMessage = sprintf("Your X-AUTH-TOKEN is: %s", $token);

            return $this->json($responseMessage, Response::HTTP_CREATED, [], [
                ObjectNormalizer::IGNORED_ATTRIBUTES => []
            ]);
        }
    }

    /**
     * @Route("/login", methods="POST")
     */
    public function getTokenAction(Request $request, UserModel $userModel): JsonResponse
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        $deserializedDataFromRequest = $serializer->deserialize($request->getContent(), User::class, 'json');

        $form = $this->createForm(UserType::class, $deserializedDataFromRequest);

        $form->submit(json_decode($request->getContent(), true));
        if (!$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);
            $errorResponse = [
                'error' => $errors
            ];
            return $this->json($errorResponse, Response::HTTP_BAD_REQUEST);
        }

        if ($form->isSubmitted()) {
            $token = $userModel->checkUserCredentials($form->getData());
            if (!$token) {
                $responseMessage = 'The username or password you entered is incorrect';
                return $this->json($responseMessage, Response::HTTP_CREATED, [], [
                    ObjectNormalizer::IGNORED_ATTRIBUTES => []
                ]);
            }

            $responseMessage = sprintf("Your X-AUTH-TOKEN is: %s", $token);

            return $this->json($responseMessage, Response::HTTP_CREATED, [], [
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