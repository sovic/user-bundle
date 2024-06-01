<?php

namespace UserBundle\UserRole;

use UserBundle\Entity\UserRole;
use UserBundle\Entity\UserRoleRelation;
use UserBundle\User\UserModelInterface;

class UserRoleManager
{
    private array $roles;

    public function __construct(private readonly UserModelInterface $user)
    {
    }

    protected function getRoles(bool $refresh = false): array
    {
        if (!$refresh && isset($this->roles)) {
            return $this->roles;
        }

        $qb = $this->user->getEntityManager()->createQueryBuilder();
        $qb->select('r')
            ->from(UserRole::class, 'r')
            ->join(UserRoleRelation::class, 'ur', 'WITH', 'r.id = ur.role')
            ->where('ur.user = :user')
            ->setParameter('user', $this->user->entity);

        $result = $qb->getQuery()->getResult();
        $this->roles = [];
        /** @var UserRole $role */
        foreach ($result as $role) {
            $this->roles[$role->getName()] = $role;
        }

        return $this->roles;
    }

    public function hasRole(string|UserRole $role): bool
    {
        $role = $this->getRole($role);
        $roles = $this->getRoles();

        return array_key_exists($role->getName(), $roles);
    }

    public function addRole(string|UserRole $role): bool
    {
        $role = $this->getRole($role);
        $roles = $this->getRoles(true);
        if (array_key_exists($role->getName(), $roles)) {
            return true;
        }

        $relation = new UserRoleRelation();
        $relation->setRole($role);
        $relation->setUser($this->user->entity);

        $em = $this->user->getEntityManager();
        $em->persist($relation);
        $em->flush();

        return true;
    }

    public function removeRole(string|UserRole $role): bool
    {
        $role = $this->getRole($role);
        $roles = $this->getRoles(true);
        if (!array_key_exists($role->getName(), $roles)) {
            return true;
        }

        $em = $this->user->getEntityManager();
        $repo = $em->getRepository(UserRoleRelation::class);
        /** @var UserRoleRelation $relation */
        $relation = $repo->findOneBy(
            [
                'role' => $role,
                'user' => $this->user->entity,
            ]
        );

        $em->remove($relation);
        $em->flush();

        return true;
    }

    private function getRole(string|UserRole $role): UserRole
    {
        if ($role instanceof UserRole) {
            return $role;
        }

        $em = $this->user->getEntityManager();
        /** @var UserRoleRepository $repo */
        $repo = $em->getRepository(UserRole::class);

        return $repo->findOneBy(
            [
                'name' => $role,
            ]
        );
    }
}
