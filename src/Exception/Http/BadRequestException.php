<?php declare(strict_types=1);

namespace PaladinBackend\Exception\Http;

class BadRequestException extends AbstractHttpClientException
{
    /**
     * @var int
     */
    protected $code = 400;
}