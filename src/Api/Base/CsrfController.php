<?php

namespace PaladinBackend\Api\Base;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PaladinBackend\Core\Cookie;
use PaladinBackend\Core\CurrentUserService;
use PaladinBackend\Core\Session;

class CsrfController extends AbstractController
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function read(
        ServerRequestInterface   $request,
        ResponseInterface        $response
    ): ResponseInterface
    {
        return $this->jsonResponse((object)["status" => true], $response);
    }
}