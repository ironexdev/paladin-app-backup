<?php

use Psr\Log\LoggerInterface;
use Paladin\Enum\EnvironmentEnum;
use Paladin\Core\Kernel;
use DI\ContainerBuilder;

if ($_ENV["ERROR_REPORTING"] === "true") {
    error_reporting(E_ALL);
    ini_set("display_errors", "On");
}

if ($_ENV["FORCE_HTTPS"] === "true") {
    if (!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] === "off") {
        echo "Website can only be accessed via HTTPS protocol";
        exit;
    }
}

const APP_DIRECTORY = __DIR__;

require_once __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

date_default_timezone_set("UTC");

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(true);
$containerBuilder->useAnnotations(true);
$containerBuilder->addDefinitions(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config-di.php");
if ($_ENV["ENVIRONMENT"] === EnvironmentEnum::PRODUCTION) {
    $containerBuilder->enableCompilation(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "var" . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . "php-di");
}

$container = $containerBuilder->build();

if ($_ENV["ENVIRONMENT"] === EnvironmentEnum::DEVELOPMENT) {
    require_once(__DIR__ . DIRECTORY_SEPARATOR . "devtools.php");
}

try {
    $container->make(Kernel::class);
} catch (Throwable $e) {
    $errorCode = $e->getCode() ?: 500;
    $logger = $container->get(LoggerInterface::class);

    $logger->error($e->getMessage(), $e->getTrace());

    if ($_ENV["ENVIRONMENT"] === EnvironmentEnum::DEVELOPMENT) {
        // Allow xhr debugging
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Origin: " . $_ENV["CLIENT_URL"]);
        header("Access-Control-Allow-Headers: X-CSRF-Token");

        if ($errorCode >= 400 && $errorCode < 500) {
            http_response_code($errorCode);
            echo $e->getMessage();
        } else {
            throw $e;
        }
    } else {
        if ($errorCode >= 400 && $errorCode < 500) {
            http_response_code($errorCode);
        } else {
            http_response_code(500);
        }
    }
}
