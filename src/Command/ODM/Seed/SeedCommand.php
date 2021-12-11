<?php

namespace Paladin\Command\ODM\Seed;

use Error;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Paladin\Enum\ResponseStatusCodeEnum;

class SeedCommand extends Command
{
    protected static $defaultName = "odm:seed";

    protected function configure(): void
    {
        $this->setDescription("Run all seeds.");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $commands = [
            $this->getApplication()->find("odm:seed:user"),
            $this->getApplication()->find("odm:seed:authentication-token")
        ];

        $emptyInput = new ArrayInput([]);

        /** @var Command $command */
        foreach($commands as $command)
        {
            try {
                $command->run($emptyInput, $output);
            } catch (\Exception $e) {
                throw new Error($e->getMessage(), ResponseStatusCodeEnum::INTERNAL_SERVER_ERROR, $e);
            }
        }

        $output->writeln("Seed data created.");

        return Command::SUCCESS;
    }
}