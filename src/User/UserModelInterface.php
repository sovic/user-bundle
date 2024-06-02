<?php

namespace UserBundle\User;

use Symfony\Component\Mime\Email;

interface UserModelInterface
{
    public function getEntity();

    public function getId(): int;

    public function setEmailVerificationCode(string $salt): void;

    public function setEmailVerified(): void;

    public function setEnabled(bool $enabled): void;

    public function activate(): void;

    public function setForgotPasswordCode(string $salt): void;

    public function getRegistrationEmail(string $template): Email;

    public function getForgotPasswordEmail(string $template): Email;
}
