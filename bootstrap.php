<?php

use project\App\Blog\Container\DIContainer;
use project\App\Blog\Repositories\PostsRepository\PostRepositoryInterface;
use project\App\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use project\App\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use project\App\Blog\Repositories\UsersRepository\UsersRepositoryInterface;

require_once __DIR__ . '/vendor/autoload.php';
$container = new DIContainer();
$container->bind( PDO::class,
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite'));
$container->bind( PostRepositoryInterface::class,
    SqlitePostsRepository::class
);
$container->bind( UsersRepositoryInterface::class,
    SqliteUsersRepository::class
);

return $container;
