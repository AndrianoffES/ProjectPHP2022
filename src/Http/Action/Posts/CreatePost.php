<?php

namespace project\App\Http\Action\Posts;

use project\App\Blog\Exceptions\AuthException;
use project\App\Http\Auth\AuthenticationInterface;
use project\App\Http\Auth\IdentificationInterface;
use project\App\Http\Auth\TokenAuthenticationInterface;
use project\App\Http\SuccessfulResponse;
use project\App\Blog\Exceptions\HttpException;
use project\App\Blog\Exceptions\InvalidArgumentException;
use project\App\Blog\Exceptions\UserNotFoundException;
use project\App\Blog\Post;
use project\App\Blog\Repositories\PostsRepository\PostRepositoryInterface;
use project\App\Blog\UUID;
use project\App\Http\Action\ActionInterface;
use project\App\Http\Request;
use project\App\Http\Response;
use project\App\Http\ErrorResponse;
use Psr\Log\LoggerInterface;

class CreatePost implements ActionInterface
{
    public function __construct(
        private PostRepositoryInterface $postsRepository,
        private TokenAuthenticationInterface $authentication,
        private LoggerInterface $logger
    ){
    }
    public function handle(Request $request): Response{
        try{
            $user = $this->authentication->user($request);
        }catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $newPostUuid = UUID::random();
        // Генерируем UUID для новой статьи

        try {
    // Пытаемся создать объект статьи из данных запроса
            $post = new Post(
                $newPostUuid,
                $user,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
    }catch (HttpException $e){
            return new ErrorResponse($e->getMessage());
        }

        $this->postsRepository->save($post);
        $this->logger->info("Post created: $newPostUuid");
        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid, ]);
    }
}