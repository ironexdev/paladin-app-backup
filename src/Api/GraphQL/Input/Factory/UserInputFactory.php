<?php

namespace Paladin\Api\GraphQL\Input\Factory;

use Paladin\Api\GraphQL\Input\Type\UserInput;

class UserInputFactory
{
    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $nickName
     * @param string $email
     * @param string $password
     * @return UserInput
     */
    public function create(
        string $firstName,
        string $lastName,
        string $nickName,
        string $email,
        string $password
    ): UserInput
    {
        $userInput = new UserInput();
        $userInput->setFirstName($firstName);
        $userInput->setLastName($lastName);
        $userInput->setNickName($nickName);
        $userInput->setEmail($email);
        $userInput->setPassword($password);

        return $userInput;
    }
}