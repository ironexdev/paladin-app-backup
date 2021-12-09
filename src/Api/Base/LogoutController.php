<?php

namespace PaladinBackend\Api\Base;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PaladinBackend\Core\Cookie;
use PaladinBackend\Core\Session;

class LogoutController extends AbstractController
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function delete(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        Session::destroy();
        Cookie::unsetToken();

        return $this->jsonResponse((object)["status" => true], $response);
    }
}