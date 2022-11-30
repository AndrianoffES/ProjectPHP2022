<?php

namespace project\App\Http\Auth;

use project\App\Blog\Exceptions\AuthException;
use project\App\Blog\Exceptions\HttpException;
use project\App\Blog\Exceptions\UserNotFoundException;
use project\App\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use project\App\Http\Request;
use project\App\Users\User;

class PasswordAuthentication implements  PasswordAuthenticationInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ){
    }

    /**
     * @throws AuthException
     * @throws \JsonException
     */
    public function user(Request $request):User
    {
        // 1. Идентифицируем пользователя
        try {
            $username = $request->jsonBodyField('username');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }
        try {
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }
        // 2. Аутентифицируем пользователя
        //    Проверяем, что предъявленный пароль
        //    соответствует сохранённому в БД
        try {
            $password = $request->jsonBodyField('password');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        if (!$user->checkPassword($password)) {
// Если пароли не совпадают — бросаем исключение
    throw new AuthException('Wrong password');
        }
        // Пользователь аутентифицирован
        return $user; }
}