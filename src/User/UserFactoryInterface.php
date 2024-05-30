<?php

namespace UserBundle\User;

use Symfony\Component\Security\Core\User\UserInterface;

interface UserFactoryInterface
{
    public function setEntityClass(string $entityClass): void;

    public function setModelClass(string $modelClass): void;

    public function loadByEntity(UserEntityInterface $entity): UserEntityModelInterface;

    public function loadByAuthUser(UserInterface $user): ?UserEntityModelInterface;

    public function loadByEmail(string $email): ?UserEntityModelInterface;

    public function loadByEmailVerificationCode(string $code): ?UserEntityModelInterface;

    public function loadByForgotPasswordCode(string $code): ?UserEntityModelInterface;
}
