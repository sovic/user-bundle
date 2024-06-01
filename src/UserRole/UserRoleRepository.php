<?php

namespace UserBundle\UserRole;

use Doctrine\ORM\EntityRepository;
use UserBundle\Entity\UserRole;

/**
 * @method UserRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserRole[]    findAll()
 * @method UserRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRoleRepository extends EntityRepository
{

}
