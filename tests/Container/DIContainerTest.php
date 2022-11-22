<?php

namespace App\Blog\UnitTests\Container;

use PHPUnit\Framework\TestCase;
use project\App\Blog\Container\DIContainer;
use project\App\Blog\Exceptions\NotFoundException;
use project\App\Blog\Repositories\UsersRepository\InMemoryRepository;
use project\App\Blog\Repositories\UsersRepository\UsersRepositoryInterface;

class DIContainerTest extends TestCase
{

    public function testItResolvesClassWithDependencies(): void {
                // Создаём объект контейнера
                $container = new DIContainer();
        // Устанавливаем правило получения
        // объекта типа SomeClassWithParameter
        $container->bind(
                SomeClassWithParameter::class,
        new SomeClassWithParameter(42) );
               // Пытаемся получить объект типа ClassDependingOnAnother
        $object = $container->get(ClassDependingOnAnother::class);
        // Проверяем, что контейнер вернул объект нужного нам типа
        $this->assertInstanceOf(
        ClassDependingOnAnother::class,
        $object );
}

    public function testItReturnsPredefinedObject(): void {
        // Создаём объект контейнера
        $container = new DIContainer();
        // Устанавливаем правило, по которому
        // всякий раз, когда контейнеру нужно
        // вернуть объект типа SomeClassWithParameter,
        // он возвращал бы предопределённый объект
    $container->bind(
        SomeClassWithParameter::class,
    new SomeClassWithParameter(42) );
       // Пытаемся получить объект типа SomeClassWithParameter
        $object = $container->get(SomeClassWithParameter::class);
        // Проверяем, что контейнер вернул
        //объект того же типа
    $this->assertInstanceOf(
SomeClassWithParameter::class,
    $object );
// Проверяем, что контейнер вернул
// тот же самый объект
    $this->assertSame(42, $object->value());
}

    /**
     * @throws NotFoundException
     */
    public function testItResolvesClassByContract(): void
    {
        // Создаём объект контейнера
        $container = new DIContainer();
        // Устанавливаем правило, по которому
        // всякий раз, когда контейнеру нужно
        // создать объект, реализующий контракт
        // UsersRepositoryInterface, он возвращал бы
        // объект класса InMemoryRepository
        $container->bind(
            UsersRepositoryInterface::class,
            InMemoryRepository::class
        );
        // Пытаемся получить объект класса,
        // реализующего контракт UsersRepositoryInterface
        $object = $container->get(UsersRepositoryInterface::class);
        // Проверяем, что контейнер вернул
        // объект класса InMemoryUsersRepository
        $this->assertInstanceOf(
            InMemoryRepository::class,
            $object );
    }

    public function testItThrowsAnExceptionIfCannotResolveType(): void{

        // Создаём объект контейнера
        $container = new DIContainer();
        // Описываем ожидаемое исключение
        $this->expectException(NotFoundException::class); $this->expectExceptionMessage(
            'Cannot resolve type: App\Blog\UnitTests\Container\SomeClass'
        );
        // Пытаемся получить объект несуществующего класса
        $container->get(SomeClass::class);
    }

    public function testItResolvesClassWithoutDependencies(): void {
        // Создаём объект контейнера
        $container = new DIContainer();
        // Пытаемся получить объект класса без зависимостей
        $object = $container->get(SomeClassWithoutDependencies::class);
        // Проверяем, что объект, который вернул контейнер имеет желаемый тип
        $this->assertInstanceOf(
            SomeClassWithoutDependencies::class,
            $object );
    }
}