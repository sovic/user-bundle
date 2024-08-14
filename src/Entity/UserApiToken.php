<?php

namespace UserBundle\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Sovic\Common\Entity\Trait\CreatedAtTrait;
use UserBundle\User\UserEntityInterface;

#[Entity]
class UserApiToken
{
    use CreatedAtTrait;

    #[Id]
    #[OneToOne(targetEntity: User::class)]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private UserEntityInterface $user;

    #[Column(type: Types::STRING, length: 255, nullable: false)]
    private string $token;

    #[Column(name: 'expiration_date', type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['default' => null])]
    private ?DateTimeImmutable $expirationDate = null;

    public function getUser(): UserEntityInterface
    {
        return $this->user;
    }

    public function setUser(UserEntityInterface $user): void
    {
        $this->user = $user;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getExpirationDate(): ?DateTimeImmutable
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?DateTimeImmutable $expirationDate): void
    {
        $this->expirationDate = $expirationDate;
    }
}
