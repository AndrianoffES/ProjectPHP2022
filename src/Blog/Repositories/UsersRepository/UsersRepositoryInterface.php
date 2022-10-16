<?php

namespace project\App\Blog\Repositories\UsersRepository;

use project\App\Blog\UUID;
use project\App\Users\User;

interface UsersRepositoryInterface
{
    public function save(User $user): void;
    public function get(UUID $uuid): User;
    public function getByUsername(string $username): User;
}