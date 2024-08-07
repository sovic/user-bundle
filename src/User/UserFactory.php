<?php

namespace UserBundle\User;

use Sovic\Common\Model\EntityModelFactory;
use UserBundle\Entity\User as UserEntity;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserFactory extends EntityModelFactory implements UserFactoryInterface
{
    protected string $entityClass = UserEntity::class;
    protected string $modelClass = User::class;

    protected MailerInterface $mailer;
    protected UserPasswordHasherInterface $passwordHasher;

    public function setEntityClass(string $entityClass): void
    {
        $this->entityClass = $entityClass;
    }

    public function setModelClass(string $modelClass): void
    {
        $this->modelClass = $modelClass;
    }

    public function __construct(
        EntityManagerInterface      $entityManager,
        MailerInterface             $mailer,
        TranslatorInterface         $translator,
        UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct($entityManager, $translator);
        $this->mailer = $mailer;
        $this->passwordHasher = $passwordHasher;
    }

    public function loadByAuthUser(UserInterface $user): UserModelInterface
    {
        // our user entity used for authentication implements UserInterface, simply load model
        $user = $this->loadEntityModel($user, $this->modelClass);
        if (null === $user) {
            throw new RuntimeException('Unable to load user model');
        }

        return $user;
    }

    public function loadByEmail(string $email): ?UserModelInterface
    {
        return $this->loadModelBy(
            $this->entityClass,
            $this->modelClass,
            [
                'email' => $email,
            ]
        );
    }

    public function loadByEmailVerificationCode(string $code): ?UserModelInterface
    {
        /** @var User $user */
        $user = $this->loadModelBy(
            $this->entityClass,
            $this->modelClass,
            [
                'emailVerificationCode' => $code,
            ]
        );
        if (null === $user) {
            return null;
        }
        $createdAt = DateTime::createFromImmutable($user->entity->getCreatedAt());
        $now = new DateTime();
        if ($now > $createdAt->modify('+7 days')) {
            return null;
        }

        return $user;
    }

    public function loadByEntity(UserEntityInterface $entity): UserModelInterface
    {
        return $this->loadEntityModel($entity, $this->modelClass);
    }

    public function loadById(int $id): ?UserModelInterface
    {
        return $this->loadModelBy(
            $this->entityClass,
            $this->modelClass,
            [
                'id' => $id,
            ]
        );
    }

    public function loadByForgotPasswordCode(string $code): ?UserModelInterface
    {
        /** @var User $user */
        $user = $this->loadModelBy(
            $this->entityClass,
            $this->modelClass,
            [
                'forgotPasswordCode' => $code,
            ]
        );

        return $user ?? null;
    }

    public function loadByUsername(string $username): ?UserModelInterface
    {
        return $this->loadModelBy(
            $this->entityClass,
            $this->modelClass,
            [
                'username' => $username,
            ]
        );
    }

    public function createNew(string $email, string $password): UserModelInterface
    {
        $user = new UserEntity();
        $user->setEmail($email);
        $user->setCreatedAt(new DateTimeImmutable());
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        return $this->loadEntityModel($user, $this->modelClass);
    }
}
