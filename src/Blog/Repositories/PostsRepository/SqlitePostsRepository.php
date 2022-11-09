<?php

namespace project\App\Blog\Repositories\PostsRepository;

use PDO;
use project\App\Blog\Exceptions\PostNotFoundException;
use project\App\Blog\Post;
use project\App\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use project\App\Blog\UUID;
use project\App\Users\User;

class SqlitePostsRepository implements PostRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection
    ){
        $this->connection = $connection;
    }


    /**
     * @param Post $post
     * @param User $uuid
     * @return void
     */
    public function save(Post $post):void
    {
        $statement = $this->connection->prepare( 'INSERT INTO posts (uuid, author, title, text)
VALUES (:uuid, :author, :title, :text)'
        );

      $statement->execute([
            ':uuid' => $post->getUuid(),
            ':author' => $post->getUser()->uuid(),
            ':title' => $post->getTitle(),
            ':text' => $post->getText()

        ]);
    }

    /**
     * @throws PostNotFoundException
     */
    public function get(UUID $uuid) :Post
    {
        $statement = $this->connection->prepare( 'SELECT * FROM posts WHERE uuid = ?');
        $statement->execute([(string)$uuid]);
      return  $this->getPost($statement,$uuid);
    }

    /**
     * @throws PostNotFoundException
     */
    private function getPost(\PDOStatement $statement, string $postUuid):Post{
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        if($result === false){
             throw new PostNotFoundException("cannot find post: $postUuid");
        }
        $userRepo = new SqliteUsersRepository($this->connection);
        $user = $userRepo->get(new UUID($result['author']));

        return new Post(
            new UUID($result['uuid']),
            $user,
            $result['title'],
            $result['text']
        );
     }

     public function delete(string $postUuid): int
     {
            $statement = $this->connection->prepare('DELETE FROM posts WHERE uuid = ? ');
            $statement->execute([(string)$postUuid]);
            return $statement->rowCount();
     }
}