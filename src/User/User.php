<?php

namespace UserBundle\User;

use DateTime;
use DateTimeImmutable;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;
use UserBundle\ORM\AbstractEntityModel;

/**
 * @method \UserBundle\Entity\User getEntity()
 */
class User extends AbstractEntityModel
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public function getId(): int
    {
        return $this->getEntity()->getId();
    }

    public function setEmailVerificationCode(string $salt): void
    {
        $entity = $this->getEntity();

        $emailVerificationCode = md5($entity->getEmail() . time() . $salt);
        $entity->setEmailVerificationCode($emailVerificationCode);

        $this->flush();
    }

    public function verifyEmail(): void
    {
        $entity = $this->getEntity();

        $entity->setEmailVerificationCode(null);
        $entity->setEmailVerificationDate(new DateTimeImmutable());

        $this->flush();
    }

    public function generateForgotPasswordCode(string $salt): void
    {
        $entity = $this->getEntity();

        $forgotPasswordCode = md5($entity->getEmail() . time() . $salt);
        $entity->setForgotPasswordCode($forgotPasswordCode);

        $this->flush();
    }

    public function getRegistrationEmail(?string $password = null): Email
    {
        $this->setEmailVerificationCode();

        $entity = $this->getEntity();
        $createDate = DateTime::createFromImmutable($entity->getCreatedDate());

        return (new TemplatedEmail())
            ->addTo($entity->getEmail())
            ->htmlTemplate('emails/signup.html.twig')
            ->subject($this->translator->trans('user.sign_up.email_subject'))
            ->context([
                'activation_code' => !$entity->isActive() ? $entity->getActivationCode() : null,
                'expiration_date' => $createDate->modify('+7 days'),
                'password' => $password,
                'subject' => $this->translator->trans('user.sign_up.email_subject'),
            ]);
    }

    public function getForgotPasswordEmail(): Email
    {
        $this->generateForgotPasswordCode();
        $entity = $this->getEntity();

        return (new TemplatedEmail())
            ->addTo($entity->getEmail())
            ->htmlTemplate('emails/forgot-password.html.twig')
            ->subject($this->translator->trans('user.forgot_password.email_subject'))
            ->context([
                'forgot_password_code' => $entity->getForgotPasswordCode(),
                'subject' => $this->translator->trans('user.forgot_password.email_subject'),
            ]);
    }

    public function usePayment(Payment $payment): void
    {
        if ($payment->isUsed()) {
            return;
        }

        $entity = $this->getEntity();
        $months = $payment->getPriceLevel()->getPremiumMonths();
        $endDate = $entity->getPremiumEndDate();
        $previousPremiumStartDate = DateTime::createFromImmutable($endDate ?? new DateTimeImmutable());
        $previousPremiumStartDate->modify('+' . $months . ' months');
        $entity->setPremiumEndDate(DateTimeImmutable::createFromMutable($previousPremiumStartDate));
        $entity->setPremium(true);
        $this->entityManager->persist($this->getEntity());
        $payment->setIsUsed(true);
        $payment->setUserId($this->getId());
        $payment->setState(Payment::STATE_PAID);
        $this->entityManager->persist($payment);
        $this->entityManager->flush();
    }

    public function canDisplayCdnFileInfo(): bool
    {
        $roles = $this->getEntity()->getRoles();

        return in_array(self::ROLE_ADMIN, $roles, true);
    }
}
