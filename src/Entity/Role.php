<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping\MappedSuperclass;
use UserBundle\Entity\Trait\RoleEntityTrait;

#[MappedSuperclass]
class Role
{
    use RoleEntityTrait;
}
