<?php declare(strict_types=1);

namespace PaladinBackend\Exception\Http;

class UnsupportedMediaTypeException extends AbstractHttpClientException
{
    /**
     * @var int
     */
    protected $code = 415;
}