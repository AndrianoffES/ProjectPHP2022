<?php

namespace project\App\Blog\Commands;

use project\App\Blog\Exceptions\ArgumentsException;
use project\App\Blog\Exceptions\CommandException;
use project\App\Blog\Exceptions\UserNotFoundException;
use project\App\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use project\App\Blog\UUID;
use project\App\Users\Name;
use project\App\Users\User;
use Psr\Log\LoggerInterface;

class CreateUserCommand
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private LoggerInterface $logger
    ){
    }

    /**
     * @throws CommandException
     * @throws ArgumentsException
     */
    public function handle(Arguments $arguments): void {
        $this->logger->info("Create user command started");

        $username = $arguments->get('username');

        // Проверяем, существует ли пользователь в репозитории
        if ($this->userExists($username)) {
// Бросаем исключение, если пользователь уже существует
            $this->logger->warning("User already exists: $username");
throw new CommandException("User already exists: $username");
        }

        // Создаём объект пользователя
        // Функция createFrom сама создаст UUID
        // и захеширует пароль
        $user = User::createFrom(
            new Name(
                $arguments->get('first_name'),
                $arguments->get('last_name') ),
            $username,
            $arguments->get('password')
        );

// Сохраняем пользователя в репозиторий
$this->usersRepository->save($user);
        $this->logger->info('User created: ' . $user->uuid());
}

    private function userExists(string $username): bool {
        try {
// Пытаемся получить пользователя из репозитория
$this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) { return false;
        }
        return true; }
}