<?php

namespace project\App\Http\Action\Users;

use project\App\Blog\Exceptions\HttpException;
use project\App\Blog\Exceptions\UserNotFoundException;
use project\App\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use project\App\Http\Action\ActionInterface;
use project\App\Http\Request;
use project\App\Http\Response;
use project\App\Http\ErrorResponse;
use project\App\Http\SuccessfulResponse;

class FindByUsername implements ActionInterface
{

// Нам понадобится репозиторий пользователей, внедряем его контракт в качестве зависимости
public function __construct(private UsersRepositoryInterface $usersRepository ){
}
// Функция, описанная в контракте
public function handle(Request $request):Response {
    try {
// Пытаемся получить искомое имя пользователя из запроса
    $username = $request->query('username');
    } catch (HttpException $e) {
// Если в запросе нет параметра username -
// возвращаем неуспешный ответ,
// сообщение об ошибке берём из описания исключения
    return new ErrorResponse($e->getMessage());
    }
    try {
// Пытаемся найти пользователя в репозитории
        $user = $this->usersRepository->getByUsername($username);
    } catch (UserNotFoundException $e) {
// Если пользователь не найден -
// возвращаем неуспешный ответ
        return new ErrorResponse($e->getMessage());
    }
    // Возвращаем успешный ответ
    return new SuccessfulResponse([
        'username' => $user->getLogin(),
        'name' => $user->getName()->getFirstName() . ' ' . $user->getName()->getLastName(),
    ]); }
}