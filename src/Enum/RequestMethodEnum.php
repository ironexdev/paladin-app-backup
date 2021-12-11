<?php declare(strict_types=1);

namespace Paladin\Enum;

use MyCLabs\Enum\Enum;

class RequestMethodEnum extends Enum
{
    const DELETE = "DELETE";
    const GET = "GET";
    const HEAD = "HEAD";
    const OPTIONS = "OPTIONS";
    const PATCH = "PATCH";
    const POST = "POST";
    const PUT = "PUT";
}