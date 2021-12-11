<?php

use DI\Container;
use GuzzleHttp\ClientInterface;
use Monolog\Formatter\JsonFormatter;
use Paladin\Cache\FilesystemCache\FilesystemCacheFactory;
use Paladin\Cache\FilesystemCache\FilesystemCacheFactoryInterface;
use Paladin\Cache\RedisCache\RedisCacheFactory;
use Paladin\Cache\RedisCache\RedisCacheFactoryInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Translation\Loader\JsonFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tuupola\Middleware\CorsMiddleware;
use Paladin\Enum\EnvironmentEnum;
use Paladin\Enum\RequestMethodEnum;
use Paladin\Core\Cookie;
use Paladin\Core\Router;
use Paladin\Core\CurrentUserService;
use Paladin\Core\Session;
use Paladin\Enum\ResponseHeaderEnum;
use Paladin\Model\Document\AuthenticationToken;
use Paladin\Model\Document\User;
use Paladin\Security\SecurityService;
use Paladin\Security\SecurityServiceInterface;
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
use TheCodingMachine\GraphQLite\Http\Psr15GraphQLMiddlewareBuilder;
use TheCodingMachine\GraphQLite\Http\WebonyxGraphqlMiddleware;
use TheCodingMachine\GraphQLite\SchemaFactory;

const DS = DIRECTORY_SEPARATOR;

return [
    /* Custom Interfaces *****************************************************/
    /*************************************************************************/
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
            Session::setSecureLogin(false); // False because user is now logged in via cookie

            return new CurrentUserService($user);
        } else {
            Cookie::unsetToken();
            return new CurrentUserService();
        }
    }),

    // Factories that create classes, which implement PSR-16
    FilesystemCacheFactoryInterface::class => DI\factory(function () {
        return new FilesystemCacheFactory(APP_DIRECTORY . DS . ".." . DS . "var" . DS . "cache");
    }),
    RedisCacheFactoryInterface::class => DI\factory(function () {
        $client = RedisAdapter::createConnection(
            "redis://" . $_ENV["REDIS_HOST"] . ":" . $_ENV["REDIS_PORT"]
        );

        return new RedisCacheFactory($client);
    }),

    // Implements PSR-15
    Router::class => DI\factory(function (Container $container, ResponseFactoryInterface $responseFactory) {
        $routes = require_once(APP_DIRECTORY . DS . ".." . DS . "config" . DS . "api" . DS . "base" . DS . "routes.php");
        return new Router($container, $responseFactory, $routes);
    }),

    SecurityServiceInterface::class => DI\create(SecurityService::class),

    // PSR interfaces ********************************************************/
    /*************************************************************************/

    // PSR-3
    LoggerInterface::class => DI\factory(function () {
        $logger = new Logger("debug");
        $fileHandler = new StreamHandler($_ENV["DEBUG_LOG"], Logger::DEBUG);
        $formatter = new JsonFormatter();
        $formatter->includeStacktraces();
        $fileHandler->setFormatter($formatter);
        $logger->pushHandler($fileHandler);

        return $logger;
    }),

    // PSR-7
    ResponseInterface::class => DI\factory(function (ResponseFactoryInterface $responseFactory) {
        return $responseFactory->createResponse();
    }),
    ResponseFactoryInterface::class => DI\create(Psr17Factory::class),

    // PSR-15
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

    // 3rd party Interfaces **************************************************/
    /*************************************************************************/

    // Guzzle
    ClientInterface::class => DI\create(GuzzleHttp\Client::class),

    // Tuupola
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

    // Doctrine
    DocumentManager::class => DI\factory(function () {
        $dbPassword = file_get_contents($_ENV["MONGO_PASSWORD_FILE"]);
        $uri = "mongodb://" . $_ENV["MONGO_USER"] . ":" . $dbPassword . "@" . $_ENV["MONGO_HOST"] . ":" . $_ENV["MONGO_PORT"] . "/" . $_ENV["MONGO_INITDB_DATABASE"];
        $client = new Client($uri, [], ["typeMap" => DocumentManager::CLIENT_TYPEMAP]);
        $config = new Configuration();

        $modelDirectory = APP_DIRECTORY . DS . "Model";
        $config->setProxyDir(APP_DIRECTORY . DS . ".." . DS . "var" . DS . "cache" . DS . "doctrine" . DS . "proxy");
        $config->setProxyNamespace("Paladin\\Model\\Proxy");
        $config->setHydratorDir(APP_DIRECTORY . DS . ".." . DS . "var" . DS . "cache" . DS . "doctrine" . DS . "hydrator");
        $config->setHydratorNamespace("Paladin\\Model\\Hydrator");
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

    // Symfony
    MailerInterface::class => DI\factory(function (LoggerInterface $logger) {
        $smtpTransport = Transport::fromDsn(
            "smtp://" . $_ENV["MAILER_USER"] . ":" . $_ENV["MAILER_PASSWORD"] . "@" . $_ENV["MAILER_HOST"] . ":" . $_ENV["MAILER_PORT"],
            null,
            null,
            $logger
        );

        return new Mailer($smtpTransport);
    }),
    TranslatorInterface::class => DI\Factory(function (JsonFileLoader $jsonFileLoader) {
        $translator = new Translator($_ENV["DEFAULT_LOCALE"]);
        $translator->addLoader("json", $jsonFileLoader);
        $translator->addResource("json", APP_DIRECTORY . DS . ".." . DS . "translations" . DS . "messages+intl-icu.en_US.json", "en_US", "messages+intl-icu");

        return $translator;
    }),
    ValidatorInterface::class => DI\factory(function (TranslatorInterface $translator) {
        return Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->setTranslator($translator)
            ->getValidator();
    }),

    // TheCodingMachine, implements PSR-15
    WebonyxGraphqlMiddleware::class => DI\factory(function (FilesystemCacheFactoryInterface $filesystemCacheFactory, ContainerInterface $container, Psr17Factory $psr17Factory) {
        $filesystemCache = $filesystemCacheFactory->create("graphql");
        $schemaFactory = new SchemaFactory($filesystemCache, $container);
        $schemaFactory->addControllerNamespace("Paladin\\Api\\GraphQL\\Controller\\")
            ->addTypeNamespace("Paladin\\Model\\Document\\")
            ->addTypeNamespace("Paladin\\Api\\GraphQL\\Input\\Type");

        $schema = $schemaFactory->createSchema();

        $builder = new Psr15GraphQLMiddlewareBuilder($schema);
        $builder->setUrl("/graphql");
        $builder->setResponseFactory($psr17Factory);
        $builder->setStreamFactory($psr17Factory);

        return $builder->createMiddleware();
    })
];