<?php

namespace UserBundle\Entity\Abstract;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
#[ORM\Table(name: "role")]
abstract class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    protected int $id;

    #[ORM\Column(type: "string", length: 50, unique: true, nullable: false)]
    protected string $name;

    #[ORM\Column(type: "string", length: 180, nullable: true, options: ["default" => null])]
    protected ?string $description = null;

    #[ORM\Column(name: "is_default", type: "boolean", nullable: false, options: ["default" => false])]
    protected bool $isDefault = false;

    #[ORM\Column(name: "is_enabled", type: "boolean", nullable: false, options: ["default" => false])]
    protected bool $isEnabled = false;

    #[ORM\Column(name: "is_deletable", type: "boolean", nullable: false, options: ["default" => true])]
    protected bool $isDeletable = true;

    #[ORM\Column(name: "create_date", type: "datetime_immutable", nullable: false)]
    protected DateTimeImmutable $createDate;

    #[ORM\Column(name: "update_date", type: "datetime_immutable", nullable: true, options: ["default" => null])]
    protected ?DateTimeImmutable $updateDate = null;

    public function __construct()
    {
        $this->createDate = new DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): void
    {
        $this->isDefault = $isDefault;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): void
    {
        $this->isEnabled = $isEnabled;
    }

    public function isDeletable(): bool
    {
        return $this->isDeletable;
    }

    public function setIsDeletable(bool $isDeletable): void
    {
        $this->isDeletable = $isDeletable;
    }

    public function getCreateDate(): DateTimeImmutable
    {
        return $this->createDate;
    }

    public function setCreateDate(DateTimeImmutable $createDate): void
    {
        $this->createDate = $createDate;
    }

    public function getUpdateDate(): ?DateTimeImmutable
    {
        return $this->updateDate;
    }

    public function setUpdateDate(?DateTimeImmutable $updateDate): void
    {
        $this->updateDate = $updateDate;
    }
}
