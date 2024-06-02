<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\UniqueConstraint;
use UserBundle\Entity\Trait\UserRoleEntityTrait;
use UserBundle\UserRole\UserRoleEntityInterface;
use UserBundle\UserRole\UserRoleRepository;


#[Table(name: 'user_role')]
#[Entity(repositoryClass: UserRoleRepository::class)]
#[UniqueConstraint(name: 'name', columns: ['name'])]
class UserRole implements UserRoleEntityInterface
{
    use UserRoleEntityTrait;
}
