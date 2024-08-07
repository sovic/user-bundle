<?php

namespace UserBundle\User;

use Symfony\Component\Mime\Email;
use UserBundle\UserRole\UserRoleManager;

/**
 * @property UserEntityInterface|\UserBundle\Entity\User $entity
 */
interface UserModelInterface
{
    public function getEntity();

    public function getId(): int;

    public function getUserRoleManager(): UserRoleManager;

    public function setEmailVerificationCode(string $salt): void;

    public function setEmailVerified(): void;

    public function isEmailVerified(): bool;

    public function setEnabled(bool $enabled): void;

    public function activate(): void;

    public function setForgotPasswordCode(string $salt): void;

    public function getRegistrationEmail(string $template): Email;

    public function getForgotPasswordEmail(string $template): Email;
}
