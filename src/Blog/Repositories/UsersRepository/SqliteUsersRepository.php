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
        // Подготавливаем запрос
        $statement = $this->connection->prepare( 'INSERT INTO users (uuid, first_name, last_name, username)
VALUES (:first_name, :last_name, :uuid, :username)'
        );
        // Выполняем запрос с конкретными значениями
       //var_dump($user = $user->getName());

        $statement->execute([
            ':uuid'=> $user->uuid(),
            ':first_name' => $user->getName()->getFirstName(),
            ':last_name' => $user->getName()->getLastName(),
            ':username'=>$user->getLogin()
        ]);
    }

    /**
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): User {
        $statement = $this->connection->prepare( 'SELECT * FROM users WHERE uuid = ?');
        $statement->execute([(string)$uuid]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        // Бросаем исключение, если пользователь не найден
        if ($result === false) {
            throw new UserNotFoundException(
                "Cannot get user: $uuid"
            ); }
        return $this->getUser($statement, $uuid);
    }

    /**
     * @param string $username
     * @return User
     * @throws UserNotFoundException
     */
    public function getByUsername(string $username): User
    {   // var_dump($username);
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username');
        $statement->execute([
            ':username'=> (string)$username
        ]);//var_dump($this->getUser($statement, $username));
       return $this->getUser($statement, $username);
    }

    public function getUuidByUsername(string $username): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username');
        $statement->execute([
            ':username'=> (string)$username
        ]);
     // $result =  $statement->fetch(PDO::FETCH_ASSOC);
      var_dump($username);
        return $this->getUser($statement, $username);
    }

    /**
     * @throws UserNotFoundException
     */
    private function getUser(\PDOStatement $statement, string $errorString): User
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
     // var_dump($result);
        if ($result === false){
            throw new UserNotFoundException("Cannot find user:$errorString");
        }
        return new User(
            new UUID($result['uuid']),
            new Name($result['first_name'], $result['last_name']),
            $result['username']

        );
    }
}