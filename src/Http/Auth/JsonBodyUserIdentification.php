<?php

namespace project\App\Http\Auth;

use project\App\Blog\Exceptions\AuthException;
use project\App\Blog\Exceptions\HttpException;
use project\App\Blog\Exceptions\UserNotFoundException;
use project\App\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use project\App\Http\Request;
use project\App\Users\User;

class JsonBodyUserIdentification implements IdentificationInterface
{
    public function __construct(private UsersRepositoryInterface $usersRepository)
    {
    }

    /**
     * @throws AuthException
     * @throws \JsonException
     */
    public function user(Request $request): User {
        try {

    $username = $request->jsonBodyField('username');
        } catch (HttpException $e) {

            throw new AuthException($e->getMessage());
        }
        try {
// Ищем пользователя в репозитории и возвращаем его
    return $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {

            throw new AuthException($e->getMessage());
        } }
}