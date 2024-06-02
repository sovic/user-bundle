<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Mapping\UniqueConstraint;
use UserBundle\Entity\Trait\UserRoleEntityTrait;
use UserBundle\UserRole\UserRoleEntityInterface;

#[MappedSuperclass]
#[UniqueConstraint(name: 'name', columns: ['name'])]
class UserRole implements UserRoleEntityInterface
{
    use UserRoleEntityTrait;
}
