<?php

namespace UserBundle\Entity\Trait;

use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use UserBundle\User\UserEntityInterface;

trait CreatorTrait
{
    #[ManyToOne(targetEntity: UserEntityInterface::class)]
    #[JoinColumn(name: 'creator_user_id', referencedColumnName: 'id', nullable: true, onDelete: 'RESTRICT')]
    public ?UserEntityInterface $creator = null;

    public function getCreator(): ?UserEntityInterface
    {
        return $this->creator;
    }

    public function setCreator(?UserEntityInterface $creator): void
    {
        $this->creator = $creator;
    }
}
