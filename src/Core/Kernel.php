<?php

namespace Paladin\Core;

use Doctrine\ODM\MongoDB\DocumentManager;
use Error;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use TheCodingMachine\GraphQLite\Http\WebonyxGraphqlMiddleware;
use Tuupola\Middleware\CorsMiddleware;
use Paladin\Enum\RequestHeaderEnum;
use Paladin\Enum\RequestMethodEnum;
use Paladin\Enum\ResponseHeaderEnum;
use Paladin\Enum\ResponseStatusCodeEnum;
use Paladin\Security\SecurityService;
use Paladin\Security\SecurityServiceInterface;

class Kernel
{
    /**
     * @param CorsMiddleware $corsMiddleware
     * @param WebonyxGraphqlMiddleware $graphQLMiddleware
     * @param ResponseInterface $defaultResponse
     * @param Router $router
     * @param ServerRequestInterface $serverRequest
     * @param SecurityServiceInterface $securityService
     */
    public function __construct(
        CorsMiddleware                   $corsMiddleware,
        WebonyxGraphqlMiddleware         $graphQLMiddleware,
        ResponseInterface                $defaultResponse,
        Router                           $router,
        ServerRequestInterface           $serverRequest,
        private SecurityServiceInterface $securityService
    )
    {
        Session::start();
        Session::regenerate();

        $this->validateCsrfToken($serverRequest, $securityService);

        $response = $this->processRequest($corsMiddleware, $graphQLMiddleware, $defaultResponse, $router, $serverRequest);

        $this->sendResponse($response->getStatusCode(), $response->getHeaders(), $response->getBody());
    }

    /**
     * @param CorsMiddleware $corsMiddleware
     * @param WebonyxGraphqlMiddleware $graphQLMiddleware
     * @param ResponseInterface $defaultResponse
     * @param Router $router
     * @param ServerRequestInterface $serverRequest
     * @return ResponseInterface
     */
    private function processRequest(
        CorsMiddleware           $corsMiddleware,
        WebonyxGraphqlMiddleware $graphQLMiddleware,
        ResponseInterface        $defaultResponse,
        Router                   $router,
        ServerRequestInterface   $serverRequest): ResponseInterface
    {
        $middlewareStack = new MiddlewareStack(
            $defaultResponse->withStatus(ResponseStatusCodeEnum::NOT_FOUND), // default/fallback response
            $corsMiddleware,
            $graphQLMiddleware,
            $router
        );

        $response = $middlewareStack->handle($serverRequest);

        // Add CSRF token to response for all GET requests
        if ($serverRequest->getMethod() === RequestMethodEnum::GET) {
            $response = $this->addCsrfToken($serverRequest, $response);
        }

        return $response;
    }

    /**
     * @param int $code
     * @param array $headers
     * @param StreamInterface $body
     */
    private function sendResponse(int $code, array $headers, StreamInterface $body)
    {
        http_response_code($code);

        foreach ($headers as $name => $values) {
            foreach ($values as $value) {
                header(sprintf("%s: %s", $name, $value), false);
            }
        }

        echo $body;
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @param SecurityServiceInterface $securityService
     */
    private function validateCsrfToken(ServerRequestInterface $serverRequest, SecurityServiceInterface $securityService): void
    {
        if (in_array($serverRequest->getMethod(), [
            RequestMethodEnum::DELETE,
            RequestMethodEnum::POST,
            RequestMethodEnum::PATCH,
            RequestMethodEnum::PUT
        ])) {
            $storedCsrfToken = Session::getCsrfToken();

            $requestCsrfToken = $serverRequest->getHeaderLine(RequestHeaderEnum::X_CSRF_TOKEN);

            if (!$storedCsrfToken || !$requestCsrfToken || !$securityService->hashEquals($storedCsrfToken, $requestCsrfToken)) {
                throw new Error("CSRF attack.", ResponseStatusCodeEnum::FORBIDDEN);
            }
        }
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    private function addCsrfToken(ServerRequestInterface $serverRequest, ResponseInterface $response): ResponseInterface
    {
        $csrfToken = Session::getCsrfToken();

        if (!$csrfToken) {
            $csrfToken = $this->securityService->csrfToken();
            Session::setCsrfToken($csrfToken);
        }

        return $response
            ->withHeader(ResponseHeaderEnum::ACCESS_CONTROL_EXPOSE_HEADERS, ResponseHeaderEnum::X_CSRF_TOKEN)
            ->withHeader(ResponseHeaderEnum::X_CSRF_TOKEN, $csrfToken);
    }
}