<?php

namespace Paladin\Api\Base;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Error;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Paladin\Enum\ResponseStatusCodeEnum;
use Paladin\Enum\TranslatorEnum;
use Paladin\Core\Cookie;
use Paladin\Core\Session;
use Paladin\Model\Document\User;
use Paladin\Model\DocumentFactory\AuthenticationTokenFactory;
use Paladin\Security\SecurityService;

class LoginController extends AbstractController
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param AuthenticationTokenFactory $authenticationTokenFactory
     * @param DocumentManager $documentManager
     * @param SecurityService $securityService
     * @return ResponseInterface
     */
    public function create(
        ServerRequestInterface     $request,
        ResponseInterface          $response,
        AuthenticationTokenFactory $authenticationTokenFactory,
        DocumentManager            $documentManager,
        SecurityService            $securityService
    ): ResponseInterface
    {
        Cookie::unsetToken();

        $requestBody = $request->getParsedBody();
        $email = $requestBody["email"] ?? null;
        $password = $requestBody["password"] ?? null;
        $remember = isset($requestBody["remember"]) && $requestBody["remember"] === "true";

        // Invalid e-mail or password
        if (!$email || !$password) {
            return $this->invalidEmailOrPasswordResponse($response);
        }

        $userRepository = $documentManager->getRepository(User::class);
        /** @var ?User $user */
        $user = $userRepository->findOneBy(["email" => $email]);

        // User with provided e-mail was not found
        if (!$user) {
            return $this->invalidEmailOrPasswordResponse($response);
        }

        // Incorrect password
        if (!$securityService->passwordVerify($password, $user->getPassword())) {
            return $this->invalidEmailOrPasswordResponse($response);
        }

        // Secure login
        Session::setUserId($user->getId());
        Session::setSecureLogin(true); // This value should be false if user is logged via authentication token stored in cookies

        // Remember user
        if ($remember) {
            $this->rememberUser($securityService, $authenticationTokenFactory, $user, $documentManager);
        }

        return $this->jsonResponse((object)["status" => true], $response);
    }

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    private function invalidEmailOrPasswordResponse(ResponseInterface $response): ResponseInterface
    {
        return $this->jsonResponse(
            (object)["error" => TranslatorEnum::INVALID_EMAIL_OR_PASSWORD],
            $response,
            ResponseStatusCodeEnum::UNPROCESSABLE_ENTITY
        );
    }

    /**
     * @param SecurityService $securityService
     * @param AuthenticationTokenFactory $authenticationTokenFactory
     * @param User $user
     * @param DocumentManager $documentManager
     */
    private function rememberUser(SecurityService $securityService, AuthenticationTokenFactory $authenticationTokenFactory, User $user, DocumentManager $documentManager): void
    {
        $selector = $securityService->bin2hex($securityService->randomBytes(16));
        $validator = $securityService->bin2hex($securityService->randomBytes(32));
        $hashedValidator = $securityService->hash("sha256", $validator);

        $authenticationToken = $authenticationTokenFactory->create($selector, $hashedValidator, $user);

        $documentManager->persist($authenticationToken);

        try {
            $documentManager->flush();
        } catch (MongoDBException $e) {
            throw new Error($e->getMessage(), ResponseStatusCodeEnum::INTERNAL_SERVER_ERROR, $e);
        }

        Cookie::setToken($selector . ":" . $validator);
    }
}