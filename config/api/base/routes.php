<?php

use PaladinBackend\Api\Base\CsrfController;
use PaladinBackend\Api\Base\IndexController;
use PaladinBackend\Api\Base\LoginController;
use PaladinBackend\Api\Base\LogoutController;
use PaladinBackend\Enum\RequestMethodEnum;

return [
    "/" => [
        RequestMethodEnum::GET => [
            "handler" => IndexController::class . "::read"
        ]
    ],
    "/login" => [
        RequestMethodEnum::POST => [
            "handler" => LoginController::class . "::create"
        ]
    ],
    "/logout" => [
        RequestMethodEnum::DELETE => [
            "handler" => LogoutController::class . "::delete"
        ]
    ],
    "/csrf" => [
        RequestMethodEnum::GET => [
            "handler" => CsrfController::class . "::read"
        ]
    ]
];