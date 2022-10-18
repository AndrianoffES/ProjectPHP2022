<?php

namespace project\App\Blog\Repositories\PostsRepository;

use PDO;
use project\App\Blog\Exceptions\PostNotFoundException;
use project\App\Blog\Post;
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
    public function save(Post $post, string $uuid):void
    {
        $statement = $this->connection->prepare( 'INSERT INTO posts (uuid, author, title, text)
VALUES (:uuid, :author, :title, :text)'
        );

      $statement->execute([
            ':uuid' => $post->getUuid(),
            ':author' => $uuid,
            ':title' => $post->getTitle(),
            ':text' => $post->getText()

        ]);
    }

    /**
     * @throws PostNotFoundException
     */
    public function get(Post $uuid) :Post
    {
        $statement = $this->connection->prepare( 'SELECT * FROM posts WHERE uuid = ?');
        $statement->execute([(string)$uuid]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            throw new PostNotFoundException(
                "Cannot get post: $uuid"
            ); }
      return  new Post(
            new UUID($result['uuid']),
            $result['author'],
            $result['text']
        );
    }
}