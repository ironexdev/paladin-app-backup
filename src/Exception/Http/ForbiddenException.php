<?php declare(strict_types=1);

namespace Paladin\Exception\Http;

class ForbiddenException extends AbstractHttpClientException
{
    /**
     * @var int
     */
    protected $code = 403;
}