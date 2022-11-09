<?php

namespace project\App\Http\Action\Posts;

use project\App\Blog\Exceptions\HttpException;
use project\App\Blog\Exceptions\InvalidArgumentException;
use project\App\Blog\Repositories\PostsRepository\PostRepositoryInterface;
use project\App\Blog\UUID;
use project\App\Http\Action\ActionInterface;
use project\App\Http\ErrorResponse;
use project\App\Http\Request;
use project\App\Http\Response;
use project\App\Http\SuccessfulResponse;

class DeletePost implements ActionInterface
{
    public function __construct(
        private PostRepositoryInterface $postsRepository){
    }
    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
       try{
           $postUuid = new UUID($request->query('uuid'));
       }catch (HttpException | InvalidArgumentException $e){
           return new ErrorResponse($e->getMessage());
       }
      $this->postsRepository->delete($postUuid);
       return new SuccessfulResponse(['post deleted' => (string)$postUuid]);

    }
}