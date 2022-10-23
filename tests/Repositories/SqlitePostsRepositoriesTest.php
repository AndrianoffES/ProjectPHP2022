<?php

namespace project\App\Blog\UnitTests\Repositories;

use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use project\App\Blog\Exceptions\PostNotFoundException;
use project\App\Blog\Post;
use project\App\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use project\App\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use project\App\Blog\UUID;
use project\App\Users\Name;
use project\App\Users\User;

class SqlitePostsRepositoriesTest extends TestCase
{
    public function testItThrowsAnExceptionWhenPostNotFound(): void {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);
        $statementStub->method('fetch')->willReturn(false);
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqlitePostsRepository($connectionMock);
        $this->expectException(PostNotFoundException::class);
        $this->expectExceptionMessage('cannot find post: 30ec7af1-7997-4c64-b921-e10121916c8d');
        $repository->get(new UUID('30ec7af1-7997-4c64-b921-e10121916c8d'));

    }

    public function testItGetPostByUuid():void{
        $connectionStub = $this->createStub(PDO::class);
        $statementPost = $this->createMock(PDOStatement::class);
        $statementUser = $this->createMock(PDOStatement::class);
        $statementPost->method('fetch')->willReturn([
            'uuid'=> '30ec7af1-7997-4c64-b921-e10121916c8d',
            'author' => '123e4567-e89b-12d3-a456-426614174000',
            'title' => 'title',
            'text' => 'text',
        ]);
        $statementUser->method('fetch')->willReturn([
            'uuid'=> '123e4567-e89b-12d3-a456-426614174000',
            'username'=>'ivan',
            'first_name' => 'Ivan',
            'last_name' => 'Ivanov'
        ]);

        $connectionStub->method('prepare')->willReturn($statementPost, $statementUser);
        $repo = new SqlitePostsRepository($connectionStub);

        $post = $repo->get(new UUID('30ec7af1-7997-4c64-b921-e10121916c8d'));

        $this->assertSame('30ec7af1-7997-4c64-b921-e10121916c8d', (string)$post->getUuid());
    }
    public function testItSavePostToDataBase():void{
        $connectionMok = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock
            ->expects($this->once()) // Ожидаем, что будет вызван один раз
            ->method('execute') // метод
            ->with([ // с единственным аргументом - массивом
                ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':author' => '123e4567-e85b-12d3-a456-416618174000',
                ':title' => 'Title',
                ':text' => 'text'
            ]);
        $connectionMok->method('prepare')->willReturn($statementMock);
        $repo = new SqlitePostsRepository($connectionMok);
        $user = new User( new UUID('123e4567-e85b-12d3-a456-416618174000'), new Name('Ivan', 'Ivanov'), 'Ivanov11');
        $repo->save(
            new Post(
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                $user,
                'Title',
                'text'
            )
        );
    }
}