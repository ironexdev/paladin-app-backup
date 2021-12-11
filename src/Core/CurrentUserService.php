<?php

namespace Paladin\Core;

use Paladin\Model\Document\User;

class CurrentUserService
{
    /**
     * @param User|null $user
     */
    public function __construct(private ?User $user = null)
    {

    }

    /**
     * Returns true if the "current" user is logged
     */
    public function isLogged(): bool
    {
        return (bool) $this->user;
    }

    /**
     * Returns an object representing the current logged user.
     * Can return null if the user is not logged.
     */
    public function getUser(): ?User
    {
        return $this->user;
    }
}