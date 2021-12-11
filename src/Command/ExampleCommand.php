<?php

namespace Paladin\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExampleCommand extends Command
{
    protected static $defaultName = "app:foo";

    protected function configure(): void
    {
        $this->addArgument("message", InputArgument::REQUIRED, "Message");
        $this->setHelp("Test command.");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln($input->getArgument("message"));

        return Command::SUCCESS;
    }
}