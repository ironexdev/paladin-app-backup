<?php declare(strict_types=1);

namespace PaladinBackend\Enum;

use MyCLabs\Enum\Enum;

class EnvironmentEnum extends Enum
{
    const DEVELOPMENT = "development";
    const TEST = "test";
    const PRODUCTION = "production";
}