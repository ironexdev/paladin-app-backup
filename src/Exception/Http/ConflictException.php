<?php declare(strict_types=1);

namespace Paladin\Exception\Http;

class ConflictException extends AbstractHttpClientException
{
    /**
     * @var int
     */
    protected $code = 409;
}