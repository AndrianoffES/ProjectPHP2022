<?php

namespace project\App\Blog\Repositories\PostsRepository;

use PDO;
use PDOException;
use project\App\Blog\Exceptions\PostNotFoundException;
use project\App\Blog\Exceptions\PostsRepositoryException;
use project\App\Blog\Post;
use project\App\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use project\App\Blog\UUID;
use project\App\Users\User;
use Psr\Log\LoggerInterface;

class SqlitePostsRepository implements PostRepositoryInterface
{


    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    ){

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
        $uuid = $post->getUuid();
      $statement->execute([
            ':uuid' => $uuid,
            ':author' => $post->getUser()->uuid(),
            ':title' => $post->getTitle(),
            ':text' => $post->getText()

        ]);
      $this->logger->info("Post saved: $uuid");
    }

    /**
     * @throws PostNotFoundException
     */
    public function get(UUID $uuid) :Post
    {
        $statement = $this->connection->prepare( 'SELECT * FROM posts WHERE uuid = ?');
        $result = $statement->execute([(string)$uuid]);
        if (!$result){
            $this->logger->warning("Cannot find post id:$uuid");
        }
      return  $this->getPost($statement,$uuid);
    }

    /**
     * @throws PostNotFoundException
     */
    private function getPost(\PDOStatement $statement, string $postUuid):Post{
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        if($result === false){
             $this->logger->warning("Cannot find post: $postUuid");
             throw new PostNotFoundException("cannot find post: $postUuid");
        }
        $userRepo = new SqliteUsersRepository($this->connection, $this->logger);
        $user = $userRepo->get(new UUID($result['author']));

        return new Post(
            new UUID($result['uuid']),
            $user,
            $result['title'],
            $result['text']
        );
     }

    /**
     * @throws PostsRepositoryException
     */
    public function delete(UUID $uuid): void {
        try {
            $statement = $this->connection->prepare(
                'DELETE FROM posts WHERE uuid = ?'
            );
            $statement->execute([(string)$uuid]); } catch (PDOException $e) {
            throw new PostsRepositoryException( $e->getMessage(), (int)$e->getCode(), $e
            ); }
    }
}