<?php

namespace Paladin\Model\DocumentFactory;

use Paladin\Api\GraphQL\Input\Type\UserInput;
use Paladin\Model\Document\User;
use Paladin\Security\SecurityServiceInterface;

class UserFactory
{
    public function __construct(private SecurityServiceInterface $securityService)
    {
    }

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $nickName
     * @param string $email
     * @param string $password
     * @return User
     */
    public function create(
        string $firstName,
        string $lastName,
        string $nickName,
        string $email,
        string $password
    ): User
    {
        $user = new User();
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setNickName($nickName);
        $user->setEmail($email);
        $user->setPassword(
            $this->securityService->passwordHash($password)
        );

        return $user;
    }

    /**
     * @param UserInput $userInput
     * @return User
     */
    public function createFromInput(UserInput $userInput): User
    {
        $user = new User();
        $user->setFirstName($userInput->getFirstName());
        $user->setLastName($userInput->getLastName());
        $user->setNickName($userInput->getNickname());
        $user->setEmail($userInput->getEmail());
        $user->setPassword(
            $this->securityService->passwordHash($userInput->getPassword())
        );

        return $user;
    }
}