<?php

namespace Paladin\Api\GraphQL\Controller;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use TheCodingMachine\GraphQLite\Annotations\Autowire;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use Paladin\Api\GraphQL\Input\Type\UserInput;
use Paladin\Model\Document\User;
use Paladin\Model\DocumentFactory\UserFactory;
use Paladin\Model\Repository\UserRepository;

class UserController extends AbstractController
{
    /**
     * @param UserInput $userInput
     * @param DocumentManager $dm
     * @param UserFactory $userFactory
     * @return User
     * @throws MongoDBException
     */
    #[Mutation]
    public function createUser(
        UserInput $userInput,
                  #[Autowire] DocumentManager $dm,
                  #[Autowire] UserFactory $userFactory
    ): User
    {
        $this->validateInput($userInput);

        $user = $userFactory->createFromInput($userInput);

        /** @var UserRepository $userRepository */
        $userRepository = $dm->getRepository(User::class);

        // TODO User already exists - client should show message that everything went well and send "login without e-mail" mail to users inbox
        if (!$userRepository->isUnique($user)) {
            return $this->secureCreateUserResponse($user);
        }

        $dm->persist($user);
        $dm->flush();

        return $this->secureCreateUserResponse($user);
    }

    /**
     * @param User $user
     * @return User
     */
    private function secureCreateUserResponse(User $user): User
    {
        // Remove password from createUser response
        $user->setPassword("/");

        return $user;
    }
}