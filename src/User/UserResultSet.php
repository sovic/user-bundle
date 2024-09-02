<?php

namespace UserBundle\User;

use Sovic\Common\ResultSet\AbstractResultSet;

/**
 * @method UserEntityInterface[] getItems()
 */
class UserResultSet extends AbstractResultSet
{
    public function toArray(): array
    {
        $users = [];
        foreach ($this->getItems() as $user) {
            $arr = [];
            $arr['id'] = $user->getId();
            $arr['create_date'] = $user->getCreatedAt();
            $arr['email'] = $user->getEmail();
            $arr['email_verification_date'] = $user->getEmailVerificationDate();
            $arr['is_enabled'] = $user->isEnabled();
            $arr['last_login_date'] = $user->getLastLoginDate();
            $arr['username'] = $user->getUsername();

            $users[] = $arr;
        }

        return $users;
    }
}
