<?php

namespace project\App\Blog\Repositories\PostsRepository;

use PDO;
use project\App\Blog\Comment;
use project\App\Blog\Exceptions\PostNotFoundException;
use project\App\Blog\Post;
use project\App\Blog\Repositories\CommentsRepository\CommentsRepositiryInterface;
use project\App\Blog\UUID;
use project\App\Users\User;

class SqliteCommentsRepository implements CommentsRepositiryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection
    ){
        $this->connection = $connection;
    }

    /**
     * @param Post $post
     * @param User $username
     * @return mixed
     */
    public function save(Comment $comment, Post $uuid):void
    {
        $statement = $this->connection->prepare( 'INSERT INTO comments (uuid, post_uuid, author_uuid, text)
VALUES (:uuid, :post_uuid, :author_uuid, :text)'
        );

        $statement->execute([
            ':uuid' => $comment->getUuid(),
            ':post_uuid' => $uuid->getPostUuid(),
            ':author_uuid' => $uuid->getAuthorUUID(),
            ':text' => $comment->getText()

        ]);
    }

    /**
     * @param Post $uuid
     * @return mixed
     */
    public function get(Comment $uuid)
    {
        $statement = $this->connection->prepare( 'SELECT * FROM comments WHERE uuid = ?');
        $statement->execute([(string)$uuid]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            throw new CommentNotFoundException(
                "Cannot get comment: $uuid"
            ); }
        return  new Comment(
            new UUID($result['uuid']),
           new UUID($result['post_uuid']),
            $result['author_uuid'],
            $result['text']
        );
    }
}