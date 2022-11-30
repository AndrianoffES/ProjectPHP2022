<?php

namespace project\App\Http\Action;

use DateTimeImmutable;
use project\App\Blog\AuthToken;
use project\App\Blog\Exceptions\AuthException;
use project\App\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use project\App\Http\Auth\PasswordAuthenticationInterface;
use project\App\Http\ErrorResponse;
use project\App\Http\Request;
use project\App\Http\Response;
use project\App\Http\SuccessfulResponse;

class LogIn implements ActionInterface
{
    public function __construct( // Авторизация по паролю
        private PasswordAuthenticationInterface $passwordAuthentication,
        // Репозиторий токенов
        private AuthTokensRepositoryInterface $authTokensRepository )
    {
    }

    public function handle(Request $request):Response {
        // Аутентифицируем пользователя
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
        // Генерируем токен
        $authToken = new AuthToken(
    // Случайная строка длиной 40 символов
        bin2hex(random_bytes(40)),
        $user->uuid(),
    // Срок годности - 1 день
    (new DateTimeImmutable())->modify('+1 day')
    );
        // Сохраняем токен в репозиторий
        $this->authTokensRepository->save($authToken);
        // Возвращаем токен
        return new SuccessfulResponse([ 'token' => (string)$authToken->token(),
        ]); }
    }