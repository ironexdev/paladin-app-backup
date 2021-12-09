<?php declare(strict_types=1);

namespace PaladinBackend\Exception\Http;

class ConflictException extends AbstractHttpClientException
{
    /**
     * @var int
     */
    protected $code = 409;
}