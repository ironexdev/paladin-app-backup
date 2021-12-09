<?php declare(strict_types=1);

namespace PaladinBackend\Exception\Http;

class ForbiddenException extends AbstractHttpClientException
{
    /**
     * @var int
     */
    protected $code = 403;
}