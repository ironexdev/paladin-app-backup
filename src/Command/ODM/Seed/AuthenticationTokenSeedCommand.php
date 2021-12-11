<?php

namespace Paladin\Command\ODM\Seed;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Error;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Paladin\Enum\ResponseStatusCodeEnum;
use Paladin\Model\Document\AuthenticationToken;
use Paladin\Model\Document\User;
use Paladin\Security\SecurityServiceInterface;

class AuthenticationTokenSeedCommand extends Command
{
    protected static $defaultName = "odm:seed:authentication-token";

    /**
     * @param DocumentManager $documentManager
     * @param SecurityServiceInterface $securityService
     */
    public function __construct(
        private DocumentManager          $documentManager,
        private SecurityServiceInterface $securityService
    )
    {
        parent::__construct(static::getDefaultName());
    }

    protected function configure(): void
    {
        $this->setDescription("Authentication Token seed.");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws MongoDBException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userRepository = $this->documentManager->getRepository(User::class);

        /** @var ?User $user */
        $user = $userRepository->findOneBy(["email" => "name@domain.com"]);

        if (!$user) {
            throw new Error("User seed has to be run before authentication token seed.", ResponseStatusCodeEnum::INTERNAL_SERVER_ERROR);
        }

        $selector = "selector"; // $this->securityService->bin2hex($this->securityService->randomBytes(16))
        $validator = "validator"; // $this->securityService->bin2hex($this->securityService->randomBytes(32)
        $hashedValidator = $this->securityService->hash("sha256", $validator);

        $authenticationToken = new AuthenticationToken();
        $authenticationToken->setActive(true);
        $authenticationToken->setSelector($selector);
        $authenticationToken->setHashedValidator($hashedValidator);
        $authenticationToken->setUser($user);

        $this->documentManager->persist($authenticationToken);

        try {
            $this->documentManager->flush();
        } catch (MongoDBException $e) {
            throw $e;
        }

        $output->writeln("Authentication Token seed finished.");

        return Command::SUCCESS;
    }
}