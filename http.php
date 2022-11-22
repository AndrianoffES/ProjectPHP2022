<?php

use project\App\Blog\Exceptions\AppException;
use project\App\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use project\App\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use project\App\Http\Action\Posts\CreateComment;
use project\App\Http\Action\Posts\CreateLike;
use project\App\Http\Action\Posts\DeletePost;
use project\App\Http\Action\Users\CreateUser;
use project\App\Http\Action\Users\FindByUsername;
use project\App\Http\ErrorResponse;
use project\App\Http\Request;
use project\App\Blog\Exceptions\HttpException;
use project\App\Http\Action\Posts\CreatePost;
use project\App\Blog\Repositories\PostsRepository\SqlitePostsRepository;


$container = require __DIR__ . '/bootstrap.php';


$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'));

try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

try {
// Пытаемся получить HTTP-метод запроса
    $method = $request->method();
} catch (HttpException) {
// Возвращаем неудачный ответ,  если по какой-то причине  не можем получить метод
    (new ErrorResponse)->send();
    return;
}

$routes = [
    'GET' => [
    '/users/show' => FindByUsername :: class
    ],

    'POST' => [
        '/users/create' =>  CreateUser :: class,
        '/post/create' =>  CreatePost :: class,
        '/comment/create' => CreateComment :: class,
        '/post/likeCreate'=> CreateLike::class
        ],
    'DELETE'=>[
        '/posts'=>  DeletePost :: class
    ]

];


if (!array_key_exists($method, $routes)) {
    (new ErrorResponse("Rout not found: $method $path"))->send();
    return; }

if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse("Rout not found: $method $path"))->send();
    return;
}

$actionClassName = $routes[$method][$path];
$action=$container->get($actionClassName);

try {
    $response = $action->handle($request);
} catch (AppException $e) {
    (new ErrorResponse($e->getMessage()))->send();
} $response->send();

