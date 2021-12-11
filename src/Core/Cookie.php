<?php

namespace Paladin\Core;

use Paladin\Enum\DateTimeEnum;

class Cookie
{
    private static string $token = "token";

    public static function export(): array
    {
        return $_COOKIE;
    }

    /**
     * @return string|null
     */
    public static function getToken(): ?string
    {
        return $_COOKIE[static::$token] ?? null;
    }

    /**
     * @param string $token
     */
    public static function setToken(string $token): void
    {
        setcookie(
            static::$token,
            $token,
            Utils::expiration(DateTimeEnum::WEEK),
            "/",
            "",
            $_ENV["ENVIRONMENT"] !== "development",
            true
        );
    }

    public static function unsetToken()
    {
        setcookie(static::$token, null, -1, "/", "");
    }
}