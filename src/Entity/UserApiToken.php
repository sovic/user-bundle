<?php

namespace UserBundle\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Sovic\Common\Entity\Trait\CreatedAtTrait;
use Sovic\Common\Entity\Trait\IdentityColumnTrait;
use Sovic\Common\Entity\Trait\UserAgentTrait;
use UserBundle\User\UserEntityInterface;

#[Index(name: 'user_id', columns: ['user_id'])]
#[UniqueConstraint(name: 'token', columns: ['token'])]
#[Entity]
class UserApiToken
{
    use IdentityColumnTrait;
    use CreatedAtTrait;
    use UserAgentTrait;

    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private UserEntityInterface $user;

    #[Column(name: 'type', type: Types::STRING, length: 100, nullable: true, options: ['default' => null])]
    private ?string $type = null;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
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
