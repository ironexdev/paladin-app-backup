<?php declare(strict_types=1);

namespace Paladin\Enum;

use MyCLabs\Enum\Enum;

class EnvironmentEnum extends Enum
{
    const DEVELOPMENT = "development";
    const TEST = "test";
    const PRODUCTION = "production";
}