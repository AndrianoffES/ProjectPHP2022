<?php


use project\App\Blog\Command\CreatePostCommand;

use project\App\Blog\Commands\Arguments;
use project\App\Blog\Commands\CreateUserCommand;
use project\App\Blog\Commands\FakeData\PopulateDB;
use project\App\Blog\Commands\posts\DeletePost;
use project\App\Blog\Commands\Users\CreateUser;
use project\App\Blog\Commands\Users\UpdateUser;
use project\App\Blog\Exceptions\AppException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;


$container = require __DIR__ . '/bootstrap.php';

//$command = $container->get(CreateUserCommand::class);
// Получаем объект логгера из контейнера
$logger = $container->get(LoggerInterface::class);
//try { $command->handle(Arguments::fromArgv($argv));
//} catch (AppException $e) {
//    $logger->error($e->getMessage(), ['exception' => $e]);
//}
//$postRepo = $container->get(SqlitePostsRepository::class);
////var_dump($postRepo);
//$post = $postRepo->get(new UUID('56c80f24-7ea9-4d83-aa1d-b9b912b4f03d'));
////var_dump($post);
//$userRepo = $container->get(SqliteUsersRepository::class);
//$user = $userRepo->get(new UUID('7928fa5f-7d22-4311-96fc-806360cb3dc1'));
////var_dump($user);

//$like = new Likes(
//    UUID::random(),
//    $post,
//    $user
//);
////var_dump($like);
//
//
//$likeRepo = $container->get(SqlLikesRepository::class);
//
//$likeRepo->checkIsExistLike($post, $user);
//if($likeRepo->checkIsExistLike($post, $user))
//$likeRepo->save($like);
////var_dump($showLikes);
///
// Создаём объект приложения
$application = new Application();
// Перечисляем классы команд
$commandsClasses = [
    CreateUser::class,
    DeletePost::class,
    UpdateUser::class,
    PopulateDB::class,
];
foreach ($commandsClasses as $commandClass) { // Посредством контейнера
// создаём объект команды
    $command = $container->get($commandClass);
    // Добавляем команду к приложению
    $application->add($command); }
// Запускаем приложение

try { $application->run();
} catch (AppException $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
}
;