<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use project\App\Blog\Container\DIContainer;
use project\App\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use project\App\Blog\Repositories\AuthTokensRepository\SqliteAuthTokensRepository;
use project\App\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use project\App\Blog\Repositories\LikesRepository\SqlLikesRepository;
use project\App\Blog\Repositories\PostsRepository\PostRepositoryInterface;
use project\App\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use project\App\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use project\App\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use project\App\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use project\App\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use project\App\Http\Auth\AuthenticationInterface;
use project\App\Http\Auth\BearerTokenAuthentication;
use project\App\Http\Auth\IdentificationInterface;
use project\App\Http\Auth\JsonBodyUserIdentification;
use project\App\Http\Auth\JsonBodyUuidIdentification;
use project\App\Http\Auth\PasswordAuthentication;
use project\App\Http\Auth\PasswordAuthenticationInterface;
use project\App\Http\Auth\TokenAuthenticationInterface;
use Psr\Log\LoggerInterface;

require_once __DIR__ . '/vendor/autoload.php';
\Dotenv\Dotenv::createImmutable(__DIR__)->safeLoad();
$container = new DIContainer();
$container->bind( PDO::class,
    new PDO('sqlite:' . __DIR__ . $_ENV['SQLITE_DB_PATH']));
$container->bind( PostRepositoryInterface::class,
    SqlitePostsRepository::class
);
$container->bind( UsersRepositoryInterface::class,
    SqliteUsersRepository::class
);

$container->bind(
    LikesRepositoryInterface::class,
    SqlLikesRepository::class);
$container->bind(
    CommentsRepositoryInterface::class,
    SqliteCommentsRepository::class
);

$logger = (new Logger('blog'));
// Включаем логирование в файлы,
// если переменная окружения LOG_TO_FILES
// // содержит значение 'yes'
if ('yes' === $_ENV['LOG_TO_FILES']) {
    $logger->pushHandler(new StreamHandler( __DIR__ . '/logs/blog.log'
    ))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.error.log', level: Logger::ERROR,
            bubble: false,
        )); }
// Включаем логирование в консоль,
// если переменная окружения LOG_TO_CONSOLE
// содержит значение 'yes'
if ('yes' === $_ENV['LOG_TO_CONSOLE']) {
    $logger ->pushHandler(
        new StreamHandler("php://stdout") );
}

$container->bind(
    LoggerInterface::class,
    $logger
);

$container->bind(
    IdentificationInterface::class,
    JsonBodyUserIdentification::class,

);
$container->bind(
    AuthenticationInterface::class,
    PasswordAuthentication::class
);

$container->bind(
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
);
$container->bind(
    AuthTokensRepositoryInterface::class,
    SqliteAuthTokensRepository::class
);
$container->bind(
    TokenAuthenticationInterface::class,
    BearerTokenAuthentication::class);
return $container;

