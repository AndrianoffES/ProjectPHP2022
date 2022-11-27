<?php

namespace project\App\Blog\Repositories\CommentsRepository;

use PDO;
use project\App\Blog\Comment;
use project\App\Blog\Exceptions\CommentNotFoundException;
use project\App\Blog\Exceptions\PostNotFoundException;
use project\App\Blog\Exceptions\UserNotFoundException;
use project\App\Blog\Post;
use project\App\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use project\App\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use project\App\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use project\App\Blog\UUID;
use project\App\Users\User;
use Psr\Log\LoggerInterface;

class SqliteCommentsRepository implements CommentsRepositoryInterface
{


    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    ){

    }

    /**
     * @param Comment $comment
     * @return void
     */
    public function save(Comment $comment):void
    {
        $statement = $this->connection->prepare( 'INSERT INTO comments (uuid, post_uuid, author_uuid, text)
VALUES (:uuid, :post_uuid, :author_uuid, :text)'
        );
        $uuid = $comment->getUuid();
        $statement->execute([
            ':uuid' => $comment->getUuid(),
            ':post_uuid' => $comment->getPostUUID()->getUuid(),
            ':author_uuid' => $comment->getAuthorUUID()->uuid(),
            ':text' => $comment->getText()

        ]);
        $this->logger->info("Comment saved: $uuid");
    }

    /**
     * @param UUID $uuid
     * @return mixed
     * @throws CommentNotFoundException
     * @throws PostNotFoundException
     */
    public function get(UUID $uuid):Comment
    {
        $statement = $this->connection->prepare( 'SELECT * FROM comments WHERE uuid = ?');
        $result = $statement->execute([(string)$uuid]);
        if(!$result){
            $this->logger->warning("Cannot find comment id: $uuid");
        }
        return $this->getComment($statement,$uuid);
    }

    /**
     * @throws CommentNotFoundException
     * @throws PostNotFoundException
     */
    private function getComment(\PDOStatement $statement, string $commentUuid) :Comment {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        if(!$result){
            $this->logger->warning("cannot find comment: $commentUuid");
            throw new CommentNotFoundException("cannot find comment: $commentUuid");

        }
        $userRepo = new SqliteUsersRepository($this->connection, $this->logger);
        $user = $userRepo->get(new UUID($result['author_uuid']));
        $postRepo = new SqlitePostsRepository($this->connection, $this->logger);
        $post = $postRepo->get(new UUID($result['post_uuid']));

        return new Comment(
            new UUID($result['uuid']),
            $post,
            $user,
            $result['text']
        );
    }
}