<?php

use project\App\Blog\Command\Arguments;
use project\App\Blog\Command\CreateUserCommand;
use project\App\Blog\UUID;
use project\App\Users\Name;
use project\App\Blog\Post;
use project\App\Users\User;
use project\App\Blog\Repositories\UsersRepository\SqliteUsersRepository;

require_once __DIR__ . '/vendor/autoload.php';
//Создаём объект подключения к SQLite
$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');
//Создаём объект репозитория
$usersRepository = new SqliteUsersRepository($connection);
//Добавляем в репозиторий несколько пользователей
//$usersRepository->save(new User(UUID::random(), new Name('Ivan', 'Nikitin'),"admin"));
//$usersRepository->save(new User(UUID::random(), new Name('Anna', 'Petrova'), "user"));
/**
try {
    echo $usersRepository->getByUsername('admlin');
} catch (Exception $e) {
    echo $e->getMessage();
}
**/
$command = new CreateUserCommand($usersRepository);
try {
    $command->handle(Arguments::fromArgv($argv));
} catch (Exception $e) {
    echo $e->getMessage();
}
