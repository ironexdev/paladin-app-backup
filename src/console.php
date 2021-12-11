#!/usr/bin/env php
<?php declare(strict_types=1);

use Paladin\Command\ExampleCommand;
use DI\ContainerBuilder;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Tools\Console\Command\ClearCache\MetadataCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateHydratorsCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\GenerateProxiesCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\QueryCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\CreateCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\DropCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\ShardCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Command\Schema\UpdateCommand;
use Doctrine\ODM\MongoDB\Tools\Console\Helper\DocumentManagerHelper;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Paladin\Command\ODM\Seed\AuthenticationTokenSeedCommand;
use Paladin\Command\ODM\Seed\SeedCommand;
use Paladin\Command\ODM\Seed\UserSeedCommand;

if ($_ENV["ERROR_REPORTING"] === "true") {
    error_reporting(E_ALL);
    ini_set("display_errors", "On");
}

const APP_DIRECTORY = __DIR__;

require_once __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(true);
$containerBuilder->useAnnotations(true);
$containerBuilder->addDefinitions(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config-di.php");

$container = $containerBuilder->build();

$helperSet = new HelperSet(    [
        "dm" => new DocumentManagerHelper($container->get(DocumentManager::class)),
    ]
);

$application = $container->get(Application::class);
$application->setHelperSet($helperSet);
$application->add($container->get(ExampleCommand::class));
$application->add($container->get(GenerateHydratorsCommand::class));
$application->add($container->get(GenerateProxiesCommand::class));
$application->add($container->get(QueryCommand::class));
$application->add($container->get(MetadataCommand::class));
$application->add($container->get(CreateCommand::class));
$application->add($container->get(DropCommand::class));
$application->add($container->get(UpdateCommand::class));
$application->add($container->get(ShardCommand::class));
$application->add($container->get(SeedCommand::class));
$application->add($container->get(UserSeedCommand::class));
$application->add($container->get(AuthenticationTokenSeedCommand::class));

$application->run();