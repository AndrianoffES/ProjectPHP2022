<?php


use project\App\Blog\Command\CreatePostCommand;

use project\App\Blog\Commands\Arguments;
use project\App\Blog\Commands\CreateUserCommand;
use project\App\Blog\Post;
use project\App\Blog\Repositories\LikesRepository\SqlLikesRepository;
use project\App\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use project\App\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use project\App\Blog\UUID;
use project\App\Blog\Likes;


$container = require __DIR__ . '/bootstrap.php';

//$command = $container->get(CreateUserCommand::class);
//
//try { $command->handle(Arguments::fromArgv($argv));
//} catch (AppException $e) { echo "{$e->getMessage()}\n";
//}
$postRepo = $container->get(SqlitePostsRepository::class);
//var_dump($postRepo);
$post = $postRepo->get(new UUID('56c80f24-7ea9-4d83-aa1d-b9b912b4f03d'));
//var_dump($post);
$userRepo = $container->get(SqliteUsersRepository::class);
$user = $userRepo->get(new UUID('7928fa5f-7d22-4311-96fc-806360cb3dc1'));
//var_dump($user);

$like = new Likes(
    UUID::random(),
    $post,
    $user
);
//var_dump($like);


$likeRepo = $container->get(SqlLikesRepository::class);

$likeRepo->checkIsExistLike($post, $user);
if($likeRepo->checkIsExistLike($post, $user))
$likeRepo->save($like);
//var_dump($showLikes);