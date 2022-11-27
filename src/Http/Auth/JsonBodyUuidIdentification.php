<?php

namespace project\App\Http\Auth;
use project\App\Blog\Exceptions\AuthException;
use project\App\Blog\Exceptions\HttpException;
use project\App\Blog\Exceptions\InvalidArgumentException;
use project\App\Blog\Exceptions\UserNotFoundException;
use project\App\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use project\App\Blog\UUID;
use project\App\Http\Request;
use project\App\Users\User;

class JsonBodyUuidIdentification implements IdentificationInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ){ }

    /**
     * @throws AuthException
     * @throws \JsonException
     */
    public function user(Request $request):User {
        try {
// Получаем UUID пользователя из JSON-тела запроса;
// ожидаем, что корректный UUID находится в поле user_uuid
    $userUuid = new UUID($request->jsonBodyField('user_uuid'));
        } catch (HttpException|InvalidArgumentException $e) {
            // Если невозможно получить UUID из запроса -
            // бросаем исключение
            throw new AuthException($e->getMessage());
        }
        try {
// Ищем пользователя в репозитории и возвращаем его
    return $this->usersRepository->get($userUuid);
        } catch (UserNotFoundException $e) {
// Если пользователь с таким UUID не найден - // бросаем исключение
            throw new AuthException($e->getMessage());
        } }
}