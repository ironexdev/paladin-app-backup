<?php declare(strict_types=1);

namespace Paladin\Enum;

use MyCLabs\Enum\Enum;

class ResponseHeaderEnum extends Enum
{
    const ACCESS_CONTROL_ALLOW_HEADERS = "Access-Control-Allow-Headers";
    const ACCESS_CONTROL_ALLOW_METHODS = "Access-Control-Allow-Methods";
    const ACCESS_ALLOW_CONTROL_ORIGIN = "Access-Control-Allow-Origin";
    const ACCESS_CONTROL_EXPOSE_HEADERS = "Access-Control-Expose-Headers";
    const ALLOW = "Allow";
    const CONTENT_LENGTH = "Content-Length";
    const CONTENT_TYPE = "Content-Type";
    const LOCATION = "Location";
    const X_CSRF_TOKEN = "X-CSRF-Token";
}