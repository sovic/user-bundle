<?php

namespace UserBundle\UserRole;

/**
 * Basic user roles, create custom roles if needed in your application and use instead
 */
enum UserRoleId: string
{
    case Admin = 'admin';
    case User = 'user';
}
