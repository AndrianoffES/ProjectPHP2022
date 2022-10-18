<?php

namespace project\App\Blog;


use project\App\Users\Name;
use project\App\Users\User;
class Post{
    private UUID $uuid;
    private string $authorUUID;
    private string $title;
    private string $text;

    public function __construct(
        UUID    $uuid,
        string   $authorUUID,
        string $title,
        string $text

    )
    {
        $this->uuid=$uuid;
        $this->text=$text;
        $this->authorUUID=$authorUUID;
        $this->title=$title;

    }

    /**
     * @return UUID
     */
    public function getPostUuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @param UUID $uuid
     */
    public function setUuid(UUID $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return User
     */
    public function getAuthorUUID(): string
    {
        return $this->authorUUID;
    }

    /**
     * @param User $authorUUID
     */
    public function setAuthorUUID(User $authorUUID): void
    {
        $this->authorUUID = $authorUUID;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }





    public function __toString()
    {
        return 'the author with id ' . $this->authorUUID . ' writing title: "' . $this->title . '" and body of post: "' . $this->text . '"';
    }

}