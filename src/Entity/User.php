<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping\MappedSuperclass;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use UserBundle\Entity\Trait\UserEntityTrait;
use UserBundle\User\UserEntityInterface;

#[MappedSuperclass]
class User implements UserInterface, UserEntityInterface, PasswordAuthenticatedUserInterface
{
    use UserEntityTrait;
}
