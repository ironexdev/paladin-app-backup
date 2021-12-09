<?php

use DI\Container;
use GuzzleHttp\ClientInterface;
use Monolog\Formatter\JsonFormatter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\Translation\Loader\JsonFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tuupola\Middleware\CorsMiddleware;
use PaladinBackend\Enum\EnvironmentEnum;
use PaladinBackend\Enum\RequestMethodEnum;
use PaladinBackend\Core\Cookie;
use PaladinBackend\Core\Router;
use PaladinBackend\Core\CurrentUserService;
use PaladinBackend\Core\Session;
use PaladinBackend\Enum\ResponseHeaderEnum;
use PaladinBackend\Mailer\Mailer;
use PaladinBackend\Mailer\MailerInterface;
use PaladinBackend\Model\Document\AuthenticationToken;
use PaladinBackend\Model\Document\User;
use PaladinBackend\Security\SecurityService;
use PaladinBackend\Security\SecurityServiceInterface;
use Cache\Adapter\Redis\RedisCachePool;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use MongoDB\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use TheCodingMachine\GraphQLite\Http\Psr15GraphQLMiddlewareBuilder;
use TheCodingMachine\GraphQLite\Http\WebonyxGraphqlMiddleware;
use TheCodingMachine\GraphQLite\SchemaFactory;

const DS = DIRECTORY_SEPARATOR;

return [
    CacheInterface::class => DI\factory(function () {
        $client = new Redis();
        $client->connect("redis", 6379);
        return new RedisCachePool($client);
    }),
    ClientInterface::class => DI\create(GuzzleHttp\Client::class),
    CorsMiddleware::class => DI\factory(function (LoggerInterface $logger) {
        return new CorsMiddleware([
            "origin" => ["*"],
            "methods" => RequestMethodEnum::toArray(),
            "headers.allow" => [
                ResponseHeaderEnum::X_CSRF_TOKEN,
                ResponseHeaderEnum::CONTENT_TYPE
            ],
            "headers.expose" => [],
            "credentials" => true,
            "cache" => 0,
            "logger" => $logger
        ]);
    }),
    CurrentUserService::class => DI\factory(function (DocumentManager $documentManager, SecurityService $securityService) {
        // Attempt to get user id from Session
        $userId = Session::getUserId();

        if ($userId) {
            $userRepository = $documentManager->getRepository(User::class);

            /** @var ?User $user */
            $user = $userRepository->findOneBy(["id" => $userId]);

            if ($user) {
                return new CurrentUserService($user);
            }
        }

        // Attempt to get user id from Cookie
        if (!$userId) {
            $token = Cookie::getToken();

            if ($token) {
                list($selector, $validator) = explode(":", $token);

                $authenticationTokenRepository = $documentManager->getRepository(AuthenticationToken::class);

                /** @var ?AuthenticationToken $authenticationToken */
                $authenticationToken = $authenticationTokenRepository->findOneBy(["selector" => $selector]);

                if ($authenticationToken) {
                    $hashedValidator = $securityService->hash("sha256", $validator);
                    $hashedAuthenticationTokenValidator = $authenticationToken->getHashedValidator();

                    $validHash = $securityService->hashEquals($hashedAuthenticationTokenValidator, $hashedValidator);

                    if ($validHash) {
                        $userId = $authenticationToken->getUser()?->getId();
                    }
                }
            }
        }

        if ($userId) {
            $userRepository = $documentManager->getRepository(User::class);

            /** @var ?User $user */
            $user = $userRepository->findOneBy(["id" => $userId]);
        }

        if (isset($user)) {
            Session::setUserId($userId);
            Session::setSecureLogin(false); // True if user is logged in via session, false if user is logged in via cookie

            return new CurrentUserService($user);
        } else {
            Cookie::unsetToken();
            return new CurrentUserService();
        }
    }),
    DocumentManager::class => DI\factory(function () {
        $dbPassword = file_get_contents($_ENV["MONGO_PASSWORD_FILE"]);
        $uri = "mongodb://" . $_ENV["MONGO_USER"] . ":" . $dbPassword . "@" . $_ENV["MONGO_HOST"] . ":" . $_ENV["MONGO_PORT"] . "/" . $_ENV["MONGO_INITDB_DATABASE"];
        $client = new Client($uri, [], ["typeMap" => DocumentManager::CLIENT_TYPEMAP]);
        $config = new Configuration();

        $modelDirectory = DEVSTACK_DIRECTORY . DS . "Model";
        $config->setProxyDir(
            DEVSTACK_DIRECTORY . DS . ".." . DS . "var" . DS . "doctrine" . DS . "proxy"
        );
        $config->setProxyNamespace("PaladinBackend\\Model\\Proxy");
        $config->setHydratorDir(
            DEVSTACK_DIRECTORY . DS . ".." . DS . "var" . DS . "doctrine" . DS . "hydrator"
        );
        $config->setHydratorNamespace("PaladinBackend\\Model\\Hydrator");
        $config->setDefaultDB($_ENV["MONGO_INITDB_DATABASE"]);
        $config->setMetadataDriverImpl(AnnotationDriver::create($modelDirectory . DS . "Document"));

        if ($_ENV["ENVIRONMENT"] === EnvironmentEnum::DEVELOPMENT) {
            $config->setAutoGenerateProxyClasses(Configuration::AUTOGENERATE_EVAL);
            $config->setAutoGenerateHydratorClasses(Configuration::AUTOGENERATE_EVAL);
        } else {
            $config->setAutoGenerateProxyClasses(Configuration::AUTOGENERATE_FILE_NOT_EXISTS);
            $config->setAutoGenerateHydratorClasses(Configuration::AUTOGENERATE_FILE_NOT_EXISTS);
        }

        // spl_autoload_register is necessary to autoload generated proxy classes. Without this, the proxy library would re-generate proxy classes for every request
        spl_autoload_register($config->getProxyManagerConfiguration()->getProxyAutoloader());

        return DocumentManager::create($client, $config);
    }),
    SecurityServiceInterface::class => DI\create(SecurityService::class),
    LoggerInterface::class => DI\factory(function () {
        $logger = new Logger("debug");
        $fileHandler = new StreamHandler($_ENV["DEBUG_LOG"], Logger::DEBUG);
        $formatter = new JsonFormatter();
        $formatter->includeStacktraces();
        $fileHandler->setFormatter($formatter);
        $logger->pushHandler($fileHandler);

        return $logger;
    }),
    MailerInterface::class => DI\factory(function () {
        $transport = new Swift_SmtpTransport($_ENV["MAILER_HOST"], $_ENV["MAILER_PORT"]);

        return new Mailer($transport, $_ENV["MAILER_USER"], file_get_contents($_ENV["MAILER_PASSWORD"]));
    }),
    ResponseInterface::class => DI\factory(function (ResponseFactoryInterface $responseFactory) {
        return $responseFactory->createResponse();
    }),
    ResponseFactoryInterface::class => DI\create(Psr17Factory::class),
    Router::class => DI\factory(function (Container $container, ResponseFactoryInterface $responseFactory) {
        $routes = require_once(DEVSTACK_DIRECTORY . DS . ".." . DS . "config" . DS . "api" . DS . "base" . DS . "routes.php");
        return new Router($container, $responseFactory, $routes);
    }),
    ServerRequestInterface::class => DI\factory(function (ClientInterface $httpClient, Psr17Factory $psr17Factory) {
        $creator = new ServerRequestCreator(
            $psr17Factory,
            $psr17Factory,
            $psr17Factory,
            $psr17Factory
        );

        return $creator->fromGlobals();
    }),
    StreamFactoryInterface::class => DI\create(Psr17Factory::class),
    TranslatorInterface::class => DI\Factory(function (JsonFileLoader $jsonFileLoader) {
        $translator = new Translator($_ENV["DEFAULT_LOCALE"]);
        $translator->addLoader("json", $jsonFileLoader);
        $translator->addResource("json", DEVSTACK_DIRECTORY . DS . ".." . DS . "translations" . DS . "messages+intl-icu.en_US.json", "en_US", "messages+intl-icu");

        return $translator;
    }),
    ValidatorInterface::class => DI\factory(function (TranslatorInterface $translator) {
        return Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->setTranslator($translator)
            ->getValidator();
    }),
    WebonyxGraphqlMiddleware::class => DI\factory(function (Psr17Factory $psr17Factory, SchemaFactory $schemaFactory) {
        $schemaFactory->addControllerNamespace("PaladinBackend\\Api\\GraphQL\\Controller\\")
            ->addTypeNamespace("PaladinBackend\\Model\\Document\\")
            ->addTypeNamespace("PaladinBackend\\Api\\GraphQL\\Input\\Type");

        $schema = $schemaFactory->createSchema();

        $builder = new Psr15GraphQLMiddlewareBuilder($schema);
        $builder->setUrl("/graphql");
        $builder->setResponseFactory($psr17Factory);
        $builder->setStreamFactory($psr17Factory);

        return $builder->createMiddleware();
    })
];