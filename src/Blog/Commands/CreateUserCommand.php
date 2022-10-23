<?php

namespace project\App\Blog\Commands;

use project\App\Blog\Exceptions\ArgumentsException;
use project\App\Blog\Exceptions\CommandException;
use project\App\Blog\Exceptions\UserNotFoundException;
use project\App\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use project\App\Blog\UUID;
use project\App\Users\Name;
use project\App\Users\User;

class CreateUserCommand
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository ){
    }

    /**
     * @throws CommandException
     * @throws ArgumentsException
     */
    public function handle(Arguments $arguments): void {
        $username = $arguments->get('username');

        // Проверяем, существует ли пользователь в репозитории
        if ($this->userExists($username)) {
// Бросаем исключение, если пользователь уже существует
throw new CommandException("User already exists: $username");
        }

// Сохраняем пользователя в репозиторий
$this->usersRepository->save(new User(
    UUID::random(),
    new Name($arguments->get('first_name'), $arguments->get('last_name')),
    $username
));
}

    private function userExists(string $username): bool {
        try {
// Пытаемся получить пользователя из репозитория
$this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) { return false;
        }
        return true; }
}