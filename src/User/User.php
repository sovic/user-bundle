<?php

namespace UserBundle\User;

use DateTime;
use DateTimeImmutable;
use LogicException;
use Sovic\Common\Model\AbstractEntityModel;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;
use UserBundle\UserRole\UserRoleManager;

/**
 * @property \UserBundle\Entity\User $entity
 * @method \UserBundle\Entity\User getEntity()
 */
class User extends AbstractEntityModel implements UserModelInterface
{
    protected UserRoleManager $userRoleManager;

    public function getId(): int
    {
        return $this->entity->getId();
    }

    public function getUserRoleManager(): UserRoleManager
    {
        if (!isset($this->userRoleManager)) {
            $this->userRoleManager = new UserRoleManager($this);
        }

        return $this->userRoleManager;
    }

    public function setEmailVerificationCode(string $salt): void
    {
        $entity = $this->entity;

        $emailVerificationCode = md5($entity->getEmail() . time() . $salt);
        $entity->setEmailVerificationCode($emailVerificationCode);

        $this->flush();
    }

    public function setEmailVerified(): void
    {
        $entity = $this->entity;

        $entity->setEmailVerificationCode(null);
        $entity->setEmailVerificationDate(new DateTimeImmutable());

        $this->flush();
    }

    public function setEnabled(bool $enabled): void
    {
        $this->entity->setIsEnabled($enabled);
        $this->flush();
    }

    public function activate(): void
    {
        $this->setEmailVerified();
        $this->setEnabled(true);
    }

    public function setForgotPasswordCode(string $salt): void
    {
        $entity = $this->entity;

        $forgotPasswordCode = md5($entity->getEmail() . time() . $salt);
        $entity->setForgotPasswordCode($forgotPasswordCode);

        $this->flush();
    }

    public function getRegistrationEmail(
        string  $template = '@UserBundle/emails/sign-up.html.twig',
        ?string $password = null
    ): Email {
        $entity = $this->entity;
        $verificationCode = $entity->getEmailVerificationCode();
        if (!$verificationCode) {
            throw new LogicException('Email verification code is not set');
        }

        $createdAt = DateTime::createFromImmutable($entity->getCreatedAt());

        return (new TemplatedEmail())
            ->addTo($entity->getEmail())
            ->htmlTemplate($template)
            ->subject($this->translator->trans('user.sign_up.email.subject', [], 'user-bundle'))
            ->context([
                'email_verification_code' => !$entity->getEmailVerificationDate() ? $verificationCode : null,
                'expiration_date' => $createdAt->modify('+7 days'),
                'password' => $password,
                'subject' => $this->translator->trans('user.sign_up.email.subject', [], 'user-bundle'),
                'recipients' => [$entity->getEmail()],
            ]);
    }

    public function getForgotPasswordEmail(
        string $template = '@UserBundle/emails/forgot-password.html.twig',
    ): Email {
        $entity = $this->entity;
        $code = $entity->getForgotPasswordCode();
        if (!$code) {
            throw new LogicException('Forgot password code is not set');
        }

        return (new TemplatedEmail())
            ->addTo($entity->getEmail())
            ->htmlTemplate($template)
            ->subject($this->translator->trans('user.forgot_password.email.subject', [], 'user-bundle'))
            ->context([
                'forgot_password_code' => $code,
                'subject' => $this->translator->trans('user.forgot_password.email.subject', [], 'user-bundle'),
                'recipients' => [$entity->getEmail()],
            ]);
    }

    public function getNewPasswordEmail(
        string $password,
        string $template = '@UserBundle/emails/new-password.html.twig',
    ): Email {
        $entity = $this->entity;

        return (new TemplatedEmail())
            ->addTo($entity->getEmail())
            ->htmlTemplate($template)
            ->subject($this->translator->trans('user.new_password.email.subject', [], 'user-bundle'))
            ->context([
                'password' => $password,
                'subject' => $this->translator->trans('user.new_password.email.subject', [], 'user-bundle'),
                'recipients' => [$entity->getEmail()],
            ]);
    }

    public function delete(): void
    {
        $this->entity->setDeletedAt(new DateTimeImmutable());
        $this->flush();
    }
}
