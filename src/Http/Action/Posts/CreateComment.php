<?php

namespace project\App\Http\Action\Posts;

use project\App\Blog\Comment;
use project\App\Blog\Exceptions\AuthException;
use project\App\Blog\Exceptions\HttpException;
use project\App\Blog\Exceptions\InvalidArgumentException;
use project\App\Blog\Exceptions\PostNotFoundException;
use project\App\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use project\App\Blog\Repositories\PostsRepository\PostRepositoryInterface;
use project\App\Blog\UUID;
use project\App\Http\Action\ActionInterface;
use project\App\Http\Auth\TokenAuthenticationInterface;
use project\App\Http\Request;
use project\App\Http\Response;
use project\App\Http\ErrorResponse;
use project\App\Http\SuccessfulResponse;
use Psr\Log\LoggerInterface;

class CreateComment implements ActionInterface
{
    public function __construct(
        private PostRepositoryInterface $postsRepository,
        private TokenAuthenticationInterface $authentication,
        private CommentsRepositoryInterface $commentsRepository,
        private LoggerInterface $logger
    ){
    }
    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {

        try {
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));


        } catch (HttpException|InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try {
            $post = $this->postsRepository->get($postUuid);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try {
            $user = $this->authentication->user($request);


        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
// Пытаемся создать объект статьи // из данных запроса
    $comment = new Comment(
                $postUuid,
                $post,
                $user,
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $this->logger->info('Start saving comment');
        $this->commentsRepository->save($comment);
        $this->logger->info('Comment saved' . $comment->getUuid());
        return new SuccessfulResponse([
            'uuid' => (string)$comment->getUuid(),]);
    }
}