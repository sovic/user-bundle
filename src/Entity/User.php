<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use UserBundle\Entity\Trait\UserEntityTrait;
use UserBundle\User\UserEntityInterface;
use UserBundle\User\UserRepository;

#[Table(name: 'user')]
#[Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, UserEntityInterface, PasswordAuthenticatedUserInterface
{
    use UserEntityTrait;
}
