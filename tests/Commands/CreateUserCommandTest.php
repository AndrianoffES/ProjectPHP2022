<?php

namespace App\Blog\UnitTests\Commands;

use App\Blog\UnitTests\DummyLogger;
use PHPUnit\Framework\TestCase;
use project\App\Blog\Commands\CreateUserCommand;
use project\App\Blog\Commands\Arguments;
use project\App\Blog\Commands\Users\CreateUser;
use project\App\Blog\Exceptions\ArgumentsException;
use project\App\Blog\Exceptions\CommandException;
use project\App\Blog\Exceptions\UserNotFoundException;
use project\App\Blog\Repositories\UsersRepository\DummyUsersRepository;
use project\App\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use project\App\Blog\UUID;
use project\App\Users\User;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class CreateUserCommandTest extends TestCase

{
    public function testItRequiresLastNameSimfony(): void {
        // Тестируем новую команду
        $command = new CreateUser( $this->makeUsersRepository(),
        );
        // Меняем тип ожидаемого исключения ..
        $this->expectException(RuntimeException::class); // .. и его сообщение
         $this->expectExceptionMessage(
        'Not enough arguments (missing: "last_name").'
);
       // Запускаем команду методом run вместо handle
        $command->run(
        // Передаём аргументы как ArrayInput,
        // а не Arguments
        // Сами аргументы не меняются
    new ArrayInput([
        'first_name' => 'Ivan', 'username' => 'Ivan', 'password' => 'some_password',
    ]),
        // Передаём также объект,
        // реализующий контракт OutputInterface
        // Нам подойдёт реализация,
        // которая ничего не делает
    new NullOutput()
); }

    public function testItRequiresPasswordSymfony(): void {
        $command = new CreateUser( $this->makeUsersRepository()
        );
        $this->expectException(RuntimeException::class); $this->expectExceptionMessage(
            'Not enough arguments (missing: "first_name, last_name, password"'
        );
        $command->run(
            new ArrayInput([
                'username' => 'Ivan', ]),
            new NullOutput() );
    }

    public function testItRequiresFirstNameSymfony(): void {
        $command = new CreateUser( $this->makeUsersRepository()
        );
        $this->expectException(RuntimeException::class); $this->expectExceptionMessage(
            'Not enough arguments (missing: "first_name, last_name").'
        );
        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password', ]),
            new NullOutput() );
    }

    public function testItSavesUserToRepositorySymfony(): void {
        $usersRepository = new class implements UsersRepositoryInterface {
            // В этом свойстве мы храним информацию о том,
            // был ли вызван метод save
            private bool $called = false;
            public function save(User $user): void {
                // Запоминаем, что метод save был вызван
                $this->called = true; }
            public function get(UUID $uuid): User {
                throw new UserNotFoundException("Not found"); }
            public function getByUsername(string $username): User {
                throw new UserNotFoundException("Not found"); }
            // Этого метода нет в контракте UsersRepositoryInterface,
            // но ничто не мешает его добавить.
            // С помощью этого метода мы можем узнать,
            // был ли вызван метод save
            public function wasCalled(): bool {
                return $this->called; }
        };
        $command = new CreateUser( $usersRepository
        );
        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
                'first_name' => 'Ivan',
                'last_name' => 'Nikitin',
            ]),
            new NullOutput() );
        $this->assertTrue($usersRepository->wasCalled());
    }

    public function testItRequiresPassword(): void {
        $command = new CreateUserCommand(
            $this->makeUsersRepository(),
            new DummyLogger()
        );
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: password');
        $command->handle(new Arguments([ 'username' => 'Ivan',
        ])); }


    // Проверяем, что команда создания пользователя бросает исключение,
    // если пользователь с таким именем уже существует
    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void {
    // Создаём объект команды
    // У команды одна зависимость - UsersRepositoryInterface
        $command = new CreateUserCommand(new DummyUsersRepository(), new DummyLogger());
    // здесь должна быть реализация UsersRepositoryInterface
        // Описываем тип ожидаемого исключения
        $this->expectException(CommandException::class);
        // и его сообщение
        $this->expectExceptionMessage('User already exists: Ivan');
        // Запускаем команду с аргументами
        $command->handle(new Arguments([
            'username' => 'Ivan',
            'password' => '123Ivan'
        ]));
    }


    private function makeUsersRepository(): UsersRepositoryInterface {
        return new class implements UsersRepositoryInterface {
            public function save(User $user): void
            {
            }
            public function get(UUID $uuid): User {
                throw new UserNotFoundException("Not found");
            }
            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }
        };
    }
    // Тест проверяет, что команда действительно требует фамилию пользователя
    public function testItRequiresLastName(): void {
        // Передаём в конструктор команды объект, возвращаемый нашей функцией
        $command = new CreateUserCommand($this->makeUsersRepository(), new DummyLogger());
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: last_name');
        $command->handle(new Arguments([
            'username' => 'Ivan',
            'password'=> '123Ivan',
        // Нам нужно передать имя пользователя,
        // чтобы дойти до проверки наличия фамилии
            'first_name' => 'Ivan',
        ]));
    }
    // Тест проверяет, что команда действительно требует имя пользователя
    public function testItRequiresFirstName(): void {
        // Вызываем ту же функцию
        $command = new CreateUserCommand($this->makeUsersRepository(), new DummyLogger());
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: first_name');
        $command->handle(new Arguments([
            'username' => 'Ivan',
            'password' => '123Ivan'
        ]));
    }

    // Тест, проверяющий, что команда сохраняет пользователя в репозитории
    public function testItSavesUserToRepository(): void {
        // Создаём объект анонимного класса
        $usersRepository = new class implements UsersRepositoryInterface {
            // В этом свойстве мы храним информацию о том,
            // был ли вызван метод save
            private bool $called = false;
            public function save(User $user): void {
                // Запоминаем, что метод save был вызван
                $this->called = true; }
            public function get(UUID $uuid): User {
                throw new UserNotFoundException("Not found"); }
            public function getByUsername(string $username): User {
                throw new UserNotFoundException("Not found"); }
            // Этого метода нет в контракте UsersRepositoryInterface,
            // но ничто не мешает его добавить.
            // С помощью этого метода мы можем узнать,
            // был ли вызван метод save
            public function wasCalled(): bool {
                return $this->called; }
        };
        // Передаём наш мок в команду
        $command = new CreateUserCommand($usersRepository, new DummyLogger());
        // Запускаем команду
        $command->handle(new Arguments([ 'username' => 'Ivan',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
            'password'=>'123Ivan',
        ]));
// Проверяем утверждение относительно мока,
// а не утверждение относительно команды
    $this->assertTrue($usersRepository->wasCalled());
    }

}