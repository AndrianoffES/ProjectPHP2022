<?php

use project\App\Blog\Command\Arguments;
use project\App\Blog\Command\CreatePostCommand;
use project\App\Blog\Command\CreateUserCommand;
use project\App\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use project\App\Blog\UUID;
use project\App\Users\Name;
use project\App\Blog\Post;
use project\App\Users\User;
use project\App\Blog\Repositories\UsersRepository\SqliteUsersRepository;

require_once __DIR__ . '/vendor/autoload.php';

$faker = Faker\Factory::create('ru_RU');

//Создаём объект подключения к SQLite
$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');
//Создаём объект репозитория
//$usersRepository = new SqliteUsersRepository($connection);
//
//try {
//    $user = $usersRepository->getByUsername('Andrew');
//} catch (UserNotFoundException $e) {
//    echo "{$e->getMessage()}\n";
//}


//$postRepositiry = new SqlitePostsRepository($connection);
//$post = $postRepositiry->get(new UUID('30ec7af1-7997-4c64-b921-e10121916c8d'));
//print_r($post);
//Добавляем в репозиторий несколько пользователей
//$usersRepository->save(new User(UUID::random(), new Name('Ivan', 'Nikitin'),"admin"));
//$usersRepository->save(new User(UUID::random(), new Name('Anna', 'Petrova'), "user"));
/**
try {
    echo $usersRepository->getByUsername('admlin');
} catch (Exception $e) {
    echo $e->getMessage();
}

$command = new CreateUserCommand($usersRepository);
try {
    $command->handle(Arguments::fromArgv($argv));
} catch (Exception $e) {
    echo $e->getMessage();
}

$user = $usersRepository->getByUsername('admin');
echo $post = new Post(
    UUID::random(),
    $user->uuid(),
    'hot news',
    'some news'
);


$postRepo = new SqlitePostsRepository($connection);
$command = new CreatePostCommand($postRepo,$usersRepository);
try {
    $command->handle(Arguments::fromArgv($argv));
} catch (Exception $e){
    echo  $e->getMessage();
}
 **/

$repo = new \project\App\Blog\Repositories\CommentsRepository\SqliteCommentsRepository($connection);

print_r($repo->get(new UUID('364f69e4-e0d8-4851-8db8-ec71f6ae90db')));