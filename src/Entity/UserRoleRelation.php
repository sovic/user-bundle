<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\MappedSuperclass;
use UserBundle\Entity\Trait\CreatedAtTrait;
use UserBundle\User\UserEntityInterface;
use UserBundle\UserRole\UserRoleEntityInterface;

#[MappedSuperclass]
#[Index(columns: ['user_id'], name: 'user_id')]
#[Index(columns: ['role_id'], name: 'role_id')]
class UserRoleRelation
{
    use CreatedAtTrait;

    #[Id]
    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    protected UserEntityInterface $user;

    #[Id]
    #[ManyToOne(targetEntity: UserRole::class)]
    #[JoinColumn(name: 'role_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    protected UserRoleEntityInterface $role;

    public function getUser(): UserEntityInterface
    {
        return $this->user;
    }

    public function setUser(UserEntityInterface $user): void
    {
        $this->user = $user;
    }

    public function getRole(): UserRoleEntityInterface
    {
        return $this->role;
    }

    public function setRole(UserRoleEntityInterface $role): void
    {
        $this->role = $role;
    }
}
