<?php

namespace project\App\Blog\Repositories\UsersRepository;

use project\App\Blog\Exceptions\UserNotFoundException;
use project\App\Blog\UUID;
use project\App\Users\Name;
use project\App\Users\User;

class DummyUsersRepository implements UsersRepositoryInterface
{
    public function save(User $user): void {
        // Ничего не делаем
    }

    /**
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): User {
        // И здесь ничего не делаем
        throw new UserNotFoundException("Not found"); }
    public function getByUsername(string $username): User {
// Нас интересует реализация только этого метода
// Для нашего теста не важно, что это будет за пользователь,
// поэтому возвращаем совершенно произвольного
        return new User(UUID::random(), new Name("first", "last"), "user123", '123Ivan');
    }
}