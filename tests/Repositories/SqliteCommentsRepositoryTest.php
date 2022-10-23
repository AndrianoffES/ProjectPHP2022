<?php

namespace project\Repositories;

use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use project\App\Blog\Comment;
use project\App\Blog\Exceptions\CommentNotFoundException;
use project\App\Blog\Post;
use project\App\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use project\App\Blog\UUID;
use project\App\Users\Name;
use project\App\Users\User;

class SqliteCommentsRepositoryTest extends TestCase
{
    public function testItSaveCommentToDataBase():void{
        $connectionMok = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock
            ->expects($this->once()) // Ожидаем, что будет вызван один раз
            ->method('execute') // метод
            ->with([ // с единственным аргументом - массивом
                ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':post_uuid' => '123e4567-e85b-12d3-a456-416618174000',
                ':author_uuid' => 'f8f30cb5-9599-49da-b4e1-6141943546f6',
                ':text' => 'text'
            ]);
        $connectionMok->method('prepare')->willReturn($statementMock);
        $repo = new SqliteCommentsRepository($connectionMok);
        $user = new User(
                new UUID('f8f30cb5-9599-49da-b4e1-6141943546f6'),
                new Name('Ivan', 'Ivanov'), 'Ivanov11');
        $post = new Post(
                new UUID('123e4567-e85b-12d3-a456-416618174000'),
                $user,
                'title',
                'text'
        );
        $repo->save(
            new Comment(
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                $post,
                $user,
                'text'
            )
        );
    }
    public function testItGetCommentByUuid():void{
        $connectionStub = $this->createStub(PDO::class);
        $statementComment = $this->createMock(PDOStatement::class);


        $statementComment->method('fetch')->willReturn([
            'uuid' => '7cb60dbe-3182-4a64-9ce2-3b5158ebe2d0',
            'author' => '123e4567-e89b-12d3-a456-426614174000',
            'title' => 'title',
            'text' => 'hello',
            'username' => 'ivan',
            'first_name' => 'Ivan', 'last_name' => 'Ivanov',
            'post_uuid' => '30ec7af1-7997-4c64-b921-e10121916c8d', 'author_uuid' => '123e4567-e89b-12d3-a456-426614174000',
        ]);


         $connectionStub->method('prepare')->willReturn($statementComment);
         $repo = new SqliteCommentsRepository($connectionStub);
         $comment = $repo->get(new UUID('7cb60dbe-3182-4a64-9ce2-3b5158ebe2d0'));
         $this->assertSame('7cb60dbe-3182-4a64-9ce2-3b5158ebe2d0', (string)$comment->getUuid());
    }
    public function testItThrowsAnExceptionWhenCommentNotFound(): void{
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);
        $statementStub->method('fetch')->willReturn(false);
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqliteCommentsRepository($connectionMock);
        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage('cannot find post: 30ec7af1-7997-4c64-b921-e10121916c8d');
        $repository->get(new UUID('30ec7af1-7997-4c64-b921-e10121916c8d'));
    }
}