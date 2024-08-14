<?php

namespace UserBundle\User;

interface UserEntityInterface
{
    public function getUserIdentifier(): string;
}
