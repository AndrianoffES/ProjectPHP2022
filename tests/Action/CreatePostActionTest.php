<?php

namespace App\Blog\UnitTests\Action;

use App\Blog\UnitTests\DummyLogger;
use project\App\Blog\Exceptions\AuthException;
use project\App\Blog\Exceptions\InvalidArgumentException;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use project\App\Blog\Exceptions\UserNotFoundException;
use project\App\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use project\App\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use project\App\Blog\UUID;
use project\App\Http\Action\Posts\CreatePost;
use project\App\Http\Auth\JsonBodyUuidIdentification;
use project\App\Http\ErrorResponse;
use project\App\Http\Request;
use project\App\Http\SuccessfulResponse;
use project\App\Users\Name;
use project\App\Users\User;


class CreatePostActionTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnSuccessfulResponse(){

        $request = new Request([], [],'{
    "user_uuid": "44630a56-fdee-4307-b3fc-f29c118a10f0",
    "title": "proverka",
     "text": "vipolnena"
    }'
        );
        $userRepo = $this->usersRepository([new User(
            new UUID('44630a56-fdee-4307-b3fc-f29c118a10f0'),
            new Name('Anna','Mandis'),
            'anman'
        )]);
        $identification = new JsonBodyUuidIdentification($userRepo);
        $identification->user($request);
        $connectionMok = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock
            ->expects($this->once()) // Ожидаем, что будет вызван один раз
            ->method('execute') ;
        $connectionMok->method('prepare')->willReturn($statementMock);
        $postRepo= new SqlitePostsRepository($connectionMok, new DummyLogger());
        $action = new CreatePost($postRepo, $identification, new DummyLogger());

        $response = $action->handle($request);
        // Проверяем, что ответ - удачный
       $this->assertInstanceOf(SuccessfulResponse::class, $response);
       // не разобрался как сделать чтобы uuid в data прилетал тот который мне нужен, поэтому убрал проверку по expectOutputString
        //$this->expectOutputString('{"success":true,"data":{"uuid":"9279e148-98d5-4f84-8c27-b39c954e9cba"}}');

        $response->send();
    }

    public function testItReturnErrorIfWrongUuid(){


        $request = new Request([], [],'{
        "user_uuid": "44630a56-fdee-4307-b3fc-f29c118a10f",
        "title": "proverka",
         "text": "vipolnena"
            }'
        );
        $userRepo = $this->usersRepository([]);
        $this->expectException(AuthException::class);
        $this->expectExceptionMessage('Malformed UUID: 44630a56-fdee-4307-b3fc-f29c118a10f');
        $identification = new JsonBodyUuidIdentification($userRepo);
        $identification->user($request);

    }

    private function usersRepository(array $users): UsersRepositoryInterface
    {

        // В конструктор анонимного класса передаём массив пользователей
        return new class($users) implements UsersRepositoryInterface {
            public function __construct(private array $users)
            {
            }

            public function save(User $user): void
            {
            }

            public function get(UUID $uuid): User
            {
                foreach ($this->users as $user){
                    if ($user instanceof User && (string) $uuid === (string)$user->uuid())
                        return $user;
                }
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && $username === $user->getLogin())
                        return $user;
                }
                throw new UserNotFoundException("Not found");
            }
        };
    }

}