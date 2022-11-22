<?php

namespace project\App\Blog\Repositories\LikesRepository;

use PDO;

use project\App\Blog\Exceptions\CommentNotFoundException;
use project\App\Blog\Exceptions\LikeAlreadyExistException;
use project\App\Blog\Exceptions\LikesNotFoundException;
use project\App\Blog\Likes;
use project\App\Blog\Post;
use project\App\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use project\App\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use project\App\Blog\UUID;
use project\App\Users\User;

class SqlLikesRepository implements LikesRepositoryInterface
{
    public function __construct(private PDO $connection)
    {
    }

    /**
     * @param Likes $likes
     * @return mixed
     */
    public function save(Likes $likes):void
    {
        $statement = $this->connection->prepare( 'INSERT INTO likes (uuid, post_uuid, user_uuid)
VALUES (:uuid, :post_uuid, :user_uuid)'
        );
        $statement->execute([
            ':uuid' => $likes->getUuid(),
            ':post_uuid' => $likes->getPostUuid()->getUuid(),
            ':user_uuid' => $likes->getUserUuid()->uuid()
        ]);
    }

    /**
     * @param UUID $uuid
     * @return mixed
     * @throws LikesNotFoundException
     */
    public function getByTargetUuid(UUID $postUuid):array
    {
        $statement = $this->connection->prepare( 'SELECT * FROM likes WHERE post_uuid = ?');
        $statement->execute([(string)$postUuid]);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        if($result === false){
            throw new LikesNotFoundException("cannot find likes: $postUuid");
        }
        $likes=[];
       foreach ($result as $item){
            $userRepo = new SqliteUsersRepository($this->connection);
            $user = $userRepo->get(new UUID($item['user_uuid']));
            $postRepo = new SqlitePostsRepository($this->connection);
            $post = $postRepo->get(new UUID($item['post_uuid']));
            $likes[] = new Likes(
             uuid:  new UUID($item['uuid']),
              postUuid:  $post,
              userUuid:  $user);

        }
        return $likes;
    }

    /**
     * @throws LikeAlreadyExistException
     */
    public function checkIsExistLike(Post $post, User $user){
        $statement = $this->connection->prepare('SELECT * FROM likes WHERE user_uuid = :user_uuid AND post_uuid = :post_uuid');
        $postUuid=(string)$post->getUuid();
        $userUuid = (string)$user->uuid();
        $statement->execute(
            [
                ':post_uuid'=>$postUuid,
                ':user_uuid'=>$userUuid
            ]
        );
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if($result){
            throw new LikeAlreadyExistException('like already exist');
        }
    }
}