<?php

use project\App\Blog\Exceptions\AppException;
use project\App\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use project\App\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use project\App\Http\Action\Posts\CreateComment;
use project\App\Http\Action\Posts\DeletePost;
use project\App\Http\Action\Users\CreateUser;
use project\App\Http\Action\Users\FindByUsername;
use project\App\Http\ErrorResponse;
use project\App\Http\Request;
use project\App\Blog\Exceptions\HttpException;
use project\App\Http\Action\Posts\CreatePost;
use project\App\Blog\Repositories\PostsRepository\SqlitePostsRepository;



require_once __DIR__ . '/vendor/autoload.php';

$request = new Request($_GET, $_SERVER, file_get_contents('php://input'));

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
    '/users/show' => new FindByUsername( new SqliteUsersRepository(
        new PDO('sqlite:' . __DIR__ . '/blog.sqlite')) ),

    ],

    'POST' => [
        '/users/create' => new CreateUser(
        new SqliteUsersRepository(
            new PDO('sqlite:' . __DIR__ . '/blog.sqlite') )
),
        '/post/create' => new CreatePost(
            new SqlitePostsRepository(
                new  PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new SqliteUsersRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
        '/comment/create' => new CreateComment(
            new SqlitePostsRepository(
                new  PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new SqliteUsersRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new SqliteCommentsRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        )
        ],
    'DELETE'=>[
        '/posts'=> new DeletePost(
            new SqlitePostsRepository(
                new  PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        )
    ]

];


if (!array_key_exists($method, $routes)) {
    (new ErrorResponse('Not found'))->send();
    return; }

if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Not found'))->send();
    return;
}

$action = $routes[$method][$path];
try {
    $response = $action->handle($request);
    $response->send();
} catch (Exception $e) {
    (new ErrorResponse($e->getMessage()))->send();
}


