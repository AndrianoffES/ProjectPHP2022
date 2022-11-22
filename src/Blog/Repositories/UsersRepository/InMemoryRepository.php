<?php
namespace project\App\Blog\Repositories\UsersRepository;
use project\App\Blog\Exceptions\UserNotFoundException;
use project\App\Blog\UUID;
use project\App\Users\User;

class InMemoryRepository implements UsersRepositoryInterface
{
    private array $users = [];

    public function save(User $user): void
    {
        $this->users[]=$user;
    }

    /**
     * @param UUID $uuid
     * @return User
     * @throws UserNotFoundException
     */

    public function get(UUID $uuid): User
    {
        foreach ($this->users as $user){
            if($user->uuid() === $uuid){
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $uuid");
    }

    public function getByUsername(string $username): User
    {
        // TODO: Implement getByUsername() method.
    }
}