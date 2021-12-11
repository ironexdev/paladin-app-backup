<?php declare(strict_types=1);

namespace Paladin\Exception\Http;

class BadRequestException extends AbstractHttpClientException
{
    /**
     * @var int
     */
    protected $code = 400;
}