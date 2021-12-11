<?php

namespace Paladin\Api\Base;

use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Paladin\Core\Cookie;
use Paladin\Core\CurrentUserService;
use Paladin\Core\Session;
use Paladin\Model\Document\User;
use Paladin\Model\DocumentFactory\UserFactory;
use Paladin\Model\Repository\UserRepository;
use Paladin\Security\SecurityServiceInterface;

class IndexController extends AbstractController
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param CurrentUserService $currentUserService
     * @return ResponseInterface
     */
    public function read(
        ServerRequestInterface   $request,
        ResponseInterface        $response,
        CurrentUserService       $currentUserService
    ): ResponseInterface
    {
        return $this->jsonResponse((object)[
            "user" => $currentUserService->getUser()?->getId(),
            "session" => Session::export(),
            "cookie" => Cookie::export()
        ], $response);
    }
}