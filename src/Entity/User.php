<?php

namespace UserBundle\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Sovic\Common\Entity\Trait\CreatedAtTrait;
use Sovic\Common\Entity\Trait\DeletedAtTrait;
use Sovic\Common\Enum\CountryId;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Length;
use UserBundle\Entity\Trait\CreatorTrait;
use UserBundle\User\UserEntityInterface;

#[MappedSuperclass]
class User implements UserInterface, UserEntityInterface, PasswordAuthenticatedUserInterface
{
    use CreatedAtTrait;
    use CreatorTrait;
    use DeletedAtTrait;

    #[Id]
    #[GeneratedValue(strategy: 'AUTO')]
    #[Column(type: "integer")]
    protected int $id;

    #[Length(max: 180)]
    #[Column(type: Types::STRING, length: 180, unique: true, nullable: false)]
    protected string $email;

    #[Length(max: 180)]
    #[Column(type: Types::STRING, length: 180, nullable: true, options: ["default" => null])]
    protected ?string $username = null;

    #[Length(max: 255)]
    #[Column(type: Types::STRING, length: 255, nullable: true, options: ["default" => null])]
    protected ?string $displayName = null;

    #[Length(max: 255)]
    #[Column(type: Types::STRING, length: 255)]
    protected string $password;

    #[Length(max: 32)]
    #[Column(name: "email_verification_code", type: Types::STRING, length: 32, unique: true, nullable: true, options: ["default" => null])]
    protected ?string $emailVerificationCode = null;

    #[Column(name: "email_verification_date", type: Types::DATETIME_IMMUTABLE, nullable: true, options: ["default" => null])]
    protected ?DateTimeImmutable $emailVerificationDate = null;

    #[Length(max: 2)]
    #[Column(name: "country_code", type: Types::STRING, length: 2, nullable: true, enumType: CountryId::class, options: ["default" => null])]
    protected ?CountryId $countryCode = null;

    #[Length(max: 3)]
    #[Column(name: "default_currency", type: Types::STRING, length: 3, nullable: true, options: ["default" => null])]
    protected ?string $defaultCurrency = null;

    #[Length(max: 10)]
    #[Column(name: "locale", type: Types::STRING, length: 10, nullable: true, options: ["default" => null])]
    protected ?string $locale = null;

    #[Column(name: "is_enabled", type: Types::BOOLEAN, nullable: false, options: ["default" => false])]
    protected bool $isEnabled = false;

    #[Column(name: "is_emailing_enabled", type: Types::BOOLEAN, nullable: false, options: ["default" => true])]
    protected bool $isEmailingEnabled = true;

    #[Column(name: "last_login_date", type: Types::DATETIME_IMMUTABLE, nullable: true, options: ["default" => null])]
    protected ?DateTimeImmutable $lastLoginDate = null;

    #[Column(name: "logins", type: Types::INTEGER, nullable: false, options: ["default" => 0])]
    protected int $logins = 0;

    #[Length(max: 32)]
    #[Column(name: "forgot_password_code", type: Types::STRING, length: 32, unique: true, nullable: true, options: ["default" => null])]
    protected ?string $forgotPasswordCode = null;

    #[Column(type: Types::JSON, nullable: false, options: ["default" => '[]'])]
    protected array $roles = [];

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

    public function getCountryCode(): ?CountryId
    {
        return $this->countryCode;
    }

    public function setCountryCode(?CountryId $countryCode): void
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

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): void
    {
        $this->isEnabled = $isEnabled;
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

    public function getLogins(): int
    {
        return $this->logins;
    }

    public function setLogins(int $logins): void
    {
        $this->logins = $logins;
    }

    public function getDefaultCurrency(): ?string
    {
        return $this->defaultCurrency;
    }

    public function setDefaultCurrency(?string $defaultCurrency): void
    {
        $this->defaultCurrency = $defaultCurrency;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): void
    {
        $this->displayName = $displayName;
    }

    public function formData(): array
    {
        return [
            'display_name' => $this->getDisplayName(),
            'is_emailing_enabled' => $this->isEmailingEnabled(),
            'username' => $this->getUsername(),
        ];
    }

    public function adminFormData(): array
    {
        return [
            'id' => $this->getId(),
            'created_at' => $this->getCreatedAt(),
            'display_name' => $this->getDisplayName(),
            'email' => $this->getEmail(),
            'email_verification_date' => $this->getEmailVerificationDate(),
            'is_emailing_enabled' => $this->isEmailingEnabled(),
            'is_enabled' => $this->isEnabled(),
            'last_login_date' => $this->getLastLoginDate(),
            'username' => $this->getUsername(),
        ];
    }
}
