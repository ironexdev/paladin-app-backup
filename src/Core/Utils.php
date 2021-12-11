<?php

namespace Paladin\Core;

use DateTimeImmutable;
use DateTimeZone;
use Error;
use Exception;
use Paladin\Enum\ResponseStatusCodeEnum;

class Utils
{
    /**
     * @param int $seconds
     * @param string $dateTimeZone
     * @return int
     */
    public static function expiration(int $seconds, string $dateTimeZone = "UTC"): int
    {
        try {
            $currentDateTime = new DateTimeImmutable("now", new DateTimeZone($dateTimeZone));
        } catch (Exception $e) {
            throw new Error($e->getMessage(), ResponseStatusCodeEnum::INTERNAL_SERVER_ERROR, $e);
        }

        return $currentDateTime->getTimestamp() + $seconds;
    }
}