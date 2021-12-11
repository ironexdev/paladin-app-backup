<?php

namespace Paladin\Api\Base;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Paladin\Core\Cookie;
use Paladin\Core\Session;

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