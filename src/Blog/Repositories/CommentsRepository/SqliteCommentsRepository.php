<?php

namespace project\App\Blog\Repositories\CommentsRepository;

use PDO;
use project\App\Blog\Comment;
use project\App\Blog\Exceptions\CommentNotFoundException;
use project\App\Blog\Exceptions\PostNotFoundException;
use project\App\Blog\Exceptions\UserNotFoundException;
use project\App\Blog\Post;
use project\App\Blog\Repositories\CommentsRepository\CommentsRepositiryInterface;
use project\App\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use project\App\Blog\Repositories\UsersRepository\SqliteUsersRepository;
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
    public function save(Comment $comment):void
    {
        $statement = $this->connection->prepare( 'INSERT INTO comments (uuid, post_uuid, author_uuid, text)
VALUES (:uuid, :post_uuid, :author_uuid, :text)'
        );

        $statement->execute([
            ':uuid' => $comment->getUuid(),
            ':post_uuid' => $comment->getPostUUID()->getUuid(),
            ':author_uuid' => $comment->getAuthorUUID()->uuid(),
            ':text' => $comment->getText()

        ]);
    }

    /**
     * @param Post $uuid
     * @return mixed
     */
    public function get(UUID $uuid):Comment
    {
        $statement = $this->connection->prepare( 'SELECT * FROM comments WHERE uuid = ?');
        $statement->execute([(string)$uuid]);
        return $this->getComment($statement,$uuid);
    }

    /**
     * @throws CommentNotFoundException
     * @throws UserNotFoundException
     * @throws PostNotFoundException
     */
    private function getComment(\PDOStatement $statement, string $commentUuid) :Comment {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        if($result === false){
            throw new CommentNotFoundException("cannot find post: $commentUuid");
        }
        $userRepo = new SqliteUsersRepository($this->connection);
        $user = $userRepo->get(new UUID($result['author_uuid']));
        $postRepo = new SqlitePostsRepository($this->connection);
        $post = $postRepo->get(new UUID($result['post_uuid']));

        return new Comment(
            new UUID($result['uuid']),
            $post,
            $user,
            $result['text']
        );
    }
}