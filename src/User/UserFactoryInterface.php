<?php

namespace UserBundle\User;

use Symfony\Component\Security\Core\User\UserInterface;

interface UserFactoryInterface
{
    public function setEntityClass(string $entityClass): void;

    public function setModelClass(string $modelClass): void;

    public function loadByEntity(UserEntityInterface $entity): UserModelInterface;

    public function loadByAuthUser(UserInterface $user): ?UserModelInterface;

    public function loadByEmail(string $email): ?UserModelInterface;

    public function loadByEmailVerificationCode(string $code): ?UserModelInterface;

    public function loadByForgotPasswordCode(string $code): ?UserModelInterface;
}
