<?php
declare(strict_types=1);

namespace App\Model;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserModel
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    private function saveData(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function addNewUser(User $user): string
    {
        $plainPassword = $user->getPassword();
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);

        $user->setPassword($hashedPassword);
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setApiToken($this->createToken());

        $this->saveData($user);

        return $user->getApiToken();
    }

    private function createToken(): string
    {
        $token = bin2hex(random_bytes(30));

        $usedToken = $this->entityManager->getRepository(User::class)->findOneBy(['apiToken' => $token]);

        if($usedToken) {
            $this->createToken();
        }
            return $token;
    }

    public function checkUserCredentials(User $user): ?string
    {
        $userFromDb = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);

        /** @var User $userFromDb */
        if(!$userFromDb) {
            return null;
        }
        $passwordIsValid = $this->passwordHasher->isPasswordValid($userFromDb, $user->getPassword());
        if($passwordIsValid === true) {
            return $userFromDb->getApiToken();
        }
    }
}