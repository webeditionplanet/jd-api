<?php

namespace AppBundle\Services;

use AppBundle\Entity\AuthToken;
use AppBundle\Entity\AuthUser;

/**
 * Class AuthToken
 */
class AuthTokenService
{
    /**
     * @param AuthUser $authUser
     *
     * @return AuthToken
     */
    public function create(AuthUser $authUser)
    {
        $authToken = new AuthToken();
        $authToken->setValue(base64_encode(random_bytes(50)));
        $authToken->setCreatedAt(new \DateTime('now'));
        $authToken->setAuthUser($authUser);

        return $authToken;
    }
}