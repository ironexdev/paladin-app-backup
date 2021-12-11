<?php

namespace Paladin\Enum;

use MyCLabs\Enum\Enum;

class DateTimeEnum extends Enum
{
    public const MINUTE = 60;
    public const HOUR = self::MINUTE * 60;
    public const DAY = self::HOUR * 24;
    public const WEEK = self::DAY * 7;
}