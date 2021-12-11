<?php

namespace Paladin\Model\DocumentFactory;

use Paladin\Model\Document\AuthenticationToken;
use Paladin\Model\Document\User;

class AuthenticationTokenFactory
{
    /**
     * @param string $selector
     * @param string $hashedValidator
     * @param User $user
     * @return AuthenticationToken
     */
    public function create(string $selector, string $hashedValidator, User $user): AuthenticationToken
    {
        $authenticationToken = new AuthenticationToken();
        $authenticationToken->setActive(true);
        $authenticationToken->setSelector($selector);
        $authenticationToken->setHashedValidator($hashedValidator);
        $authenticationToken->setUser($user);

        return $authenticationToken;
    }
}