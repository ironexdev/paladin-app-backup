<?php

namespace Paladin\Core;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

// Based on https://github.com/idealo/php-middleware-stack
class MiddlewareStack implements RequestHandlerInterface
{
    /**
     * @var MiddlewareInterface[]
     */
    protected array $middlewares = [];

    /**
     * @param ResponseInterface $defaultResponse
     * @param MiddlewareInterface ...$middlewares
     */
    public function __construct(private ResponseInterface $defaultResponse, MiddlewareInterface ...$middlewares)
    {
        $this->middlewares = $middlewares;
    }

    /**
     * @param MiddlewareInterface $middleware
     * @return RequestHandlerInterface
     */
    private function withoutMiddleware(MiddlewareInterface $middleware): RequestHandlerInterface
    {
        return new self(
            $this->defaultResponse,
            ...array_filter(
                $this->middlewares,
                function ($m) use ($middleware) {
                    return $middleware !== $m;
                }
            )
        );
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->middlewares[0] ?? null;

        return $middleware ? $middleware->process(
                $request,
                $this->withoutMiddleware($middleware)
            ) : $this->defaultResponse;
    }
}