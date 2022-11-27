<?php

namespace App\Blog\UnitTests\Action\Repositories;

use App\Blog\UnitTests\DummyLogger;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use project\App\Blog\Exceptions\UserNotFoundException;
use project\App\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use project\App\Blog\UUID;
use project\App\Users\Name;
use project\App\Users\User;

class SqliteUsersRepositoryTest extends TestCase
{
// Тест, проверяющий, что SQLite-репозиторий бросает исключение, когда запрашиваемый пользователь не найден
    public function testItThrowsAnExceptionWhenUserNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);
        $statementStub->method('fetch')->willReturn(false);
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqliteUsersRepository($connectionMock, new DummyLogger());
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('Cannot find user: Ivan');
        $repository->getByUserName('Ivan');


    }

    public function testItSavesUserToDatabase(): void {
        // 2. Создаём стаб подключения
        $connectionStub = $this->createStub(PDO::class);
// 4. Создаём мок запроса, возвращаемый стабом подключения
        $statementMock = $this->createMock(PDOStatement::class);
// 5. Описываем ожидаемое взаимодействие нашего репозитория с моком запроса
     $statementMock
        ->expects($this->once()) // Ожидаем, что будет вызван один раз
         ->method('execute') // метод
        ->with([ // с единственным аргументом - массивом
            ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
            ':first_name' => 'Ivan',
            ':last_name' => 'Nikitin',
             ':username' => 'ivan123'
        ]);
// 3. При вызове метода prepare стаб подключения возвращает мок запроса
    $connectionStub->method('prepare')->willReturn($statementMock);
        // 1. Передаём в репозиторий стаб подключения

        $repository = new SqliteUsersRepository($connectionStub, new DummyLogger());
        // Вызываем метод сохранения пользователя
        $repository->save(
            new User( // Свойства пользователя точно такие,
            // как и в описании мока
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                new Name('Ivan', 'Nikitin'),
                'ivan123'

            ));
    }

    public function testItGetUserByUsername():void{
        $connectionStub = $this->createStub(PDO::class);
        $statementUser = $this->createMock(PDOStatement::class);

        $statementUser->method('fetch')->willReturn([
            'uuid'=> '30ec7af1-7997-4c64-b921-e10121916c8d',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
            'username' => 'ivan123',
        ]);
        $connectionStub->method('prepare')->willReturn($statementUser);
        $repo = new SqliteUsersRepository($connectionStub, new DummyLogger());
        $user = $repo->getByUsername('ivan123');
        $this->assertSame('30ec7af1-7997-4c64-b921-e10121916c8d', (string)$user->uuid());

    }
}