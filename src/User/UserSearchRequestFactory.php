<?php

namespace UserBundle\User;

use Sovic\Common\DataList\AbstractSearchRequestFactory;
use Symfony\Component\HttpFoundation\Request;

class UserSearchRequestFactory extends AbstractSearchRequestFactory
{
    public function createFromRequest(Request $request): UserSearchRequest
    {
        $searchRequest = new UserSearchRequest();
        $this->loadDefaultSearchRequest($request, $searchRequest);

        return $searchRequest;
    }
}
