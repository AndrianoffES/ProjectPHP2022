<?php

namespace project\App\Users;
use DateTimeImmutable;
use project\App\Blog\UUID;
use project\App\Users\Name;

class User{
    private UUID $uuid;
    private Name $name;
    private string $username;

    /**
     * @param UUID $uuid
     * @param Name $name
     * @param string $login
     */

    public function __construct(
        UUID $uuid,
        Name   $name,
        string $login
    )
    {
        $this->uuid=$uuid;
        $this->name = $name;
        $this->username = $login;

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


}