<?php declare(strict_types=1);

namespace Paladin\Exception\Http;

class UnauthorizedException extends AbstractHttpClientException
{
    /**
     * @var int
     */
    protected $code = 401;
}