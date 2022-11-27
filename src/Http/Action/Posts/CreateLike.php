<?php

namespace project\App\Http\Action\Posts;

use project\App\Blog\Exceptions\HttpException;
use project\App\Blog\Exceptions\InvalidArgumentException;
use project\App\Blog\Exceptions\LikeAlreadyExistException;
use project\App\Blog\Exceptions\PostNotFoundException;
use project\App\Blog\Exceptions\UserNotFoundException;
use project\App\Blog\Likes;
use project\App\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use project\App\Blog\Repositories\PostsRepository\PostRepositoryInterface;
use project\App\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use project\App\Blog\UUID;
use project\App\Http\Action\ActionInterface;
use project\App\Http\ErrorResponse;
use project\App\Http\Request;
use project\App\Http\Response;
use project\App\Http\SuccessfulResponse;

class CreateLike implements ActionInterface
{
    public function __construct(
        private PostRepositoryInterface $postsRepository,
        private UsersRepositoryInterface $usersRepository,
        private LikesRepositoryInterface $likesRepository
    ){
    }
    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        try{
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));

        }catch (HttpException|InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());

    }
        try {
            $post = $this->postsRepository->get($postUuid);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try{
            $userUuid = new UUID($request->jsonBodyField('user_uuid'));
        }catch (HttpException|InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try{
           $user = $this->usersRepository->get($userUuid);
        }catch (UserNotFoundException $e){
        return new ErrorResponse($e->getMessage());
        }



        try{
        $this->likesRepository->checkIsExistLike($post,$user);
        }catch (LikeAlreadyExistException $e){
            return new ErrorResponse($e->getMessage());
        }
        try{
            $like = new Likes(
                $uuid = UUID::random(),
                $post,
                $user
            );
        }
        catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $this->likesRepository->save($like);
        return new SuccessfulResponse([
            'uuid' => (string)$postUuid,]);

    }
}