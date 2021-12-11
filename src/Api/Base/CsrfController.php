<?php

namespace Paladin\Api\Base;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Paladin\Core\Cookie;
use Paladin\Core\CurrentUserService;
use Paladin\Core\Session;

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