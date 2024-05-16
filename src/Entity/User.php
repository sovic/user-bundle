<?php

namespace UserBundle\Entity;

use Doctrine\DBAL\Types\Types;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use UserBundle\User\UserRepository;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 180, unique: true, nullable: false)]
    private string $email;

    #[ORM\Column(type: Types::STRING, length: 180, nullable: true, options: ["default" => null])]
    private ?string $username = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $password;

    #[ORM\Column(name: "create_date", type: Types::DATETIME_IMMUTABLE, nullable: false, options: ["default" => "CURRENT_DATE()"])]
    private DateTimeImmutable $createDate;

    #[ORM\Column(name: "email_verification_code", type: Types::STRING, length: 32, unique: true, nullable: true, options: ["default" => null])]
    private ?string $emailVerificationCode = null;

    #[ORM\Column(name: "email_verification_date", type: Types::DATETIME_IMMUTABLE, nullable: true, options: ["default" => null])]
    private ?DateTimeImmutable $emailVerificationDate = null;

    #[ORM\Column(name: "country_code", type: Types::STRING, length: 2, nullable: true, options: ["default" => null])]
    private ?string $countryCode = null;

    #[ORM\Column(name: "locale", type: Types::STRING, length: 10, nullable: true, options: ["default" => null])]
    private ?string $locale = null;

    #[ORM\Column(name: "is_emailing_enabled", type: Types::BOOLEAN, nullable: false, options: ["default" => true])]
    private bool $isEmailingEnabled = true;

    #[ORM\Column(name: "last_login_date", type: Types::DATETIME_IMMUTABLE, nullable: true, options: ["default" => null])]
    private ?DateTimeImmutable $lastLoginDate = null;

    #[ORM\Column(name: "forgot_password_code", type: Types::STRING, length: 32, unique: true, nullable: true, options: ["default" => null])]
    private ?string $forgotPasswordCode = null;

    private array $roles = [];

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getCreateDate(): DateTimeImmutable
    {
        return $this->createDate;
    }

    public function setCreateDate(DateTimeImmutable $createDate): void
    {
        $this->createDate = $createDate;
    }

    public function getEmailVerificationCode(): ?string
    {
        return $this->emailVerificationCode;
    }

    public function setEmailVerificationCode(?string $emailVerificationCode): void
    {
        $this->emailVerificationCode = $emailVerificationCode;
    }

    public function getEmailVerificationDate(): ?DateTimeImmutable
    {
        return $this->emailVerificationDate;
    }

    public function setEmailVerificationDate(?DateTimeImmutable $emailVerificationDate): void
    {
        $this->emailVerificationDate = $emailVerificationDate;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

    public function isEmailingEnabled(): bool
    {
        return $this->isEmailingEnabled;
    }

    public function setIsEmailingEnabled(bool $isEmailingEnabled): void
    {
        $this->isEmailingEnabled = $isEmailingEnabled;
    }

    public function getLastLoginDate(): ?DateTimeImmutable
    {
        return $this->lastLoginDate;
    }

    public function setLastLoginDate(?DateTimeImmutable $lastLoginDate): void
    {
        $this->lastLoginDate = $lastLoginDate;
    }

    public function getForgotPasswordCode(): ?string
    {
        return $this->forgotPasswordCode;
    }

    public function setForgotPasswordCode(?string $forgotPasswordCode): void
    {
        $this->forgotPasswordCode = $forgotPasswordCode;
    }
}
