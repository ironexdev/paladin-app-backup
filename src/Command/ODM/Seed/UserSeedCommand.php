<?php

namespace Paladin\Command\ODM\Seed;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Paladin\Model\Document\User;
use Paladin\Security\SecurityServiceInterface;

class UserSeedCommand extends Command
{
    protected static $defaultName = "odm:seed:user";

    /**
     * @param DocumentManager $documentManager
     * @param SecurityServiceInterface $securityService
     */
    public function __construct(
        private DocumentManager $documentManager,
        private SecurityServiceInterface $securityService
    )
    {
        parent::__construct(static::getDefaultName());
    }

    protected function configure(): void
    {
        $this->setDescription("User seed.");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws MongoDBException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User();
        $user->setFirstName("First Name");
        $user->setLastName("Last Name");
        $user->setNickName("Nickname");
        $user->setEmail("name@domain.com");
        $user->setPassword($this->securityService->passwordHash("password123456"));
        $user->setActive(true);

        $this->documentManager->persist($user);

        try {
            $this->documentManager->flush();
        } catch (MongoDBException $e) {
            throw $e;
        }

        $output->writeln("User seed finished.");

        return Command::SUCCESS;
    }
}