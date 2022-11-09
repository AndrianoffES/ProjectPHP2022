<?php

namespace project\App\Http\Action\Users;

use project\App\Blog\Exceptions\HttpException;
use project\App\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use project\App\Blog\UUID;
use project\App\Http\Action\ActionInterface;
use project\App\Http\ErrorResponse;
use project\App\Http\Request;
use project\App\Http\Response;
use project\App\Http\SuccessfulResponse;
use project\App\Users\Name;
use project\App\Users\User;


class CreateUser implements ActionInterface
{
    public function __construct(
        private SqliteUsersRepository $usersRepository
    )
    {
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        try{
            $newUserUuid = UUID::random();
            $user = new User(
                $newUserUuid,
                new Name(
                    $request->jsonBodyField('first_name'),
                    $request->jsonBodyField('last_name')
                ),
                $request->jsonBodyField('username'),
            );
        } catch (HttpException $e){
            return new ErrorResponse($e->getMessage());
    }
        $this->usersRepository->save($user);
        return new SuccessfulResponse([
            'uuid'=>(string)$newUserUuid,
        ]);
    }
}