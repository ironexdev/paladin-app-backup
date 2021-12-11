<?php

namespace Paladin\Api\Base;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Paladin\Enum\ResponseStatusCodeEnum;

abstract class AbstractController
{
    /**
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param StreamFactoryInterface $streamFactory
     */
    public function __construct(
        protected LoggerInterface $logger,
        protected TranslatorInterface $translator,
        private StreamFactoryInterface $streamFactory)
    {
    }

    /**
     * @param object $parameters
     * @param ResponseInterface $response
     * @param int $status
     * @param array $headers
     * @return ResponseInterface
     */
    protected function jsonResponse(
        object            $parameters,
        ResponseInterface $response,
        int               $status = ResponseStatusCodeEnum::OK,
        array             $headers = []
    ): ResponseInterface
    {
        foreach ($headers as $key => $value) {
            $response->withHeader($key, $value);
        }

        $responseBody = $this->streamFactory->createStream(json_encode($parameters));

        return $response
            ->withStatus($status)
            ->withHeader("Content-Type", "application/json")
            ->withBody($responseBody);
    }
}