<?php

namespace project\App\Blog\Command;

use project\App\Blog\Exceptions\ArgumentsException;
use project\App\Blog\Exceptions\CommandException;
use project\App\Blog\Exceptions\UserNotFoundException;
use project\App\Blog\Post;
use project\App\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use project\App\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use project\App\Blog\UUID;


class CreatePostCommand
{
public function __construct(
   private SqlitePostsRepository $PostsRepository,
   private SqliteUsersRepository $usersRepository
){
    }

    /**
     * @throws CommandException
     * @throws ArgumentsException
     */

    public function handle(Arguments $arguments): void {
        $username = $arguments->get('username');

            if (!$this->userExists($username)){
                //var_dump($username);
                throw new CommandException("User not exists: $username");

            }
                $author_uuid = $this->usersRepository->getUuidByUsername($username)->uuid();


// Сохраняем post в репозиторий
        $this->PostsRepository->save(new Post(
            UUID::random(),
            $author_uuid,
            $arguments->get('title'),
            $arguments->get('text')
        ),$author_uuid);
    }
    private function userExists(string $username): bool {
        //var_dump($username);
        try {
// Пытаемся получить пользователя из репозитория
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) { return false;
        }
        return true; }
}