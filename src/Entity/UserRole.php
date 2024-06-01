<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping\MappedSuperclass;
use UserBundle\Entity\Trait\UserRoleEntityTrait;

#[MappedSuperclass]
class UserRole
{
    use UserRoleEntityTrait;
}
