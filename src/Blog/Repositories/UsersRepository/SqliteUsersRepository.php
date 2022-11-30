<?php

namespace project\App\Blog\Repositories\UsersRepository;
use PDO;
use project\App\Blog\UUID;
use project\App\Users\Name;
use project\App\Users\User;
use project\App\Blog\Exceptions\UserNotFoundException;
use Psr\Log\LoggerInterface;

class SqliteUsersRepository implements UsersRepositoryInterface
{


    public function __construct(
      private  PDO $connection,
      private  LoggerInterface $logger
    ){

    }
    public function save(User $user): void {

        $statement = $this->connection->prepare( 'INSERT INTO users (uuid, first_name, last_name, username, password)
VALUES (:uuid, :first_name, :last_name,  :username, :password) 
ON CONFLICT (uuid) DO UPDATE SET first_name = :first_name, last_name = :last_name'
        );

        $uuid = $user->uuid();
        $statement->execute([
            ':uuid'=> $uuid,
            ':first_name' => $user->getName()->getFirstName(),
            ':last_name' => $user->getName()->getLastName(),
            ':username'=>$user->getLogin(),
            ':password'=>$user->hashPassword()
        ]);
        $this->logger->info("User saved:$uuid");
    }


    public function get(UUID $uuid): User {
        $statement = $this->connection->prepare( 'SELECT * FROM users WHERE uuid = ?');
        $result = $statement->execute([(string)$uuid]);
        if (!$result){
            $this->logger->warning("Cannot find user:$uuid");
        }

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
       $result = $statement->execute([
            ':username'=> $username
        ]);
        if(!$result){
            $this->logger->warning("Cannot find user:$username");
        }
       return $this->getUser($statement, $username);
    }


    /**
     * @throws UserNotFoundException
     */
    private function getUser(\PDOStatement $statement, string $username): User
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$result){
            $this->logger->warning("Cannot find user: $username");
            throw new UserNotFoundException("Cannot find user: $username");
        }
        return new User(
            new UUID($result['uuid']),
            new Name($result['first_name'], $result['last_name']),
            $result['username'],
            $result['password']

        );
    }
}