<?php

namespace PaladinBackend\Api\Base;

use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PaladinBackend\Core\Cookie;
use PaladinBackend\Core\CurrentUserService;
use PaladinBackend\Core\Session;
use PaladinBackend\Model\Document\User;
use PaladinBackend\Model\DocumentFactory\UserFactory;
use PaladinBackend\Model\Repository\UserRepository;
use PaladinBackend\Security\SecurityServiceInterface;

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