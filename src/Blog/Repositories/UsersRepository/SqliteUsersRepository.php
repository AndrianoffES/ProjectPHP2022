<?php

namespace project\App\Blog\Repositories\UsersRepository;
use PDO;
use project\App\Blog\UUID;
use project\App\Users\Name;
use project\App\Users\User;
use project\App\Blog\Exceptions\UserNotFoundException;

class SqliteUsersRepository implements UsersRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection
    ){
        $this->connection = $connection;
    }
    public function save(User $user): void {

        $statement = $this->connection->prepare( 'INSERT INTO users (uuid, first_name, last_name, username)
VALUES (:uuid, :first_name, :last_name,  :username)'
        );


        $statement->execute([
            ':uuid'=> $user->uuid(),
            ':first_name' => $user->getName()->getFirstName(),
            ':last_name' => $user->getName()->getLastName(),
            ':username'=>$user->getLogin()
        ]);
    }


    public function get(UUID $uuid): User {
        $statement = $this->connection->prepare( 'SELECT * FROM users WHERE uuid = ?');
        $statement->execute([(string)$uuid]);

        return $this->getUser($statement, $uuid);
    }

    /**
     * @param string $username
     * @return User
     * @throws UserNotFoundException
     */
    public function getByUsername(string $username): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username');
        $statement->execute([
            ':username'=> $username
        ]);
       return $this->getUser($statement, $username);
    }


    /**
     * @throws UserNotFoundException
     */
    private function getUser(\PDOStatement $statement, string $errorString): User
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$result){
            throw new UserNotFoundException("Cannot find user: $errorString");
        }
        return new User(
            new UUID($result['uuid']),
            new Name($result['first_name'], $result['last_name']),
            $result['username']

        );
    }
}