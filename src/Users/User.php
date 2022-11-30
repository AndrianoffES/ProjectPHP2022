<?php

namespace project\App\Users;
use DateTimeImmutable;
use project\App\Blog\AuthToken;
use project\App\Blog\UUID;
use project\App\Users\Name;


class User{

    public function __construct(
    private UUID $uuid,
    private Name $name,
    private string $username,
    private string $hashedPassword,

    ){

}


    /**
     * @return UUID
     */
    public function uuid(): UUID
    {
        return $this->uuid;
    }


    /**
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }

    /**
     * @param Name $name
     */
    public function setName(Name $name): void
    {
        $this->username = $name;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->username;
    }

    /**
     * @param string $login
     */
    public function setLogin(string $login): void
    {
        $this->username = $login;
    }


    public function __toString() : string
    {
        return "User $this->uuid with name $this->name and login $this->username" . PHP_EOL;
    }

    public function hashPassword(): string
    {
        return $this->hashedPassword;
    }
    private static function hash(UUID $uuid, string $password): string
    {
        return hash('sha256', $uuid . $password);
    }
    public function checkPassword(string $password): bool {
        return $this->hashedPassword === self::hash($this->uuid,$password);
    }

    // Функция для создания нового пользователя
    public static function createFrom(
        Name $name,
        string $username,
        string $password,

    ): self
    {   $uuid = UUID::random();
        return new self(
            $uuid,
            $name,
            $username,
            self::hash($uuid,$password),
        );
    }


}