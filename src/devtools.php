<?php

use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

// Pretty errors
$whoops = $container->get(Run::class);
$whoops->pushHandler($container->get(PrettyPageHandler::class));
$whoops->register();

// Pretty print
function pretty_print($value)
{
    $valueType = gettype($value);

    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Origin: " . $_ENV["CLIENT_URL"]);
    header("Access-Control-Allow-Headers: *");

    if ($valueType === "array") {
        echo "<pre>" . print_r($value, true) . "</pre>";
    } else if ($valueType === "object") {
        var_export($value);

    } else if ($valueType === "boolean") {
        echo $value ? "true" : "false";
    } else {
        echo $value;
    }
}