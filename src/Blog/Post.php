<?php

namespace project\App\Blog;


use project\App\Blog\UUID;
use project\App\Users\User;
class Post{
    private UUID $uuid;
    private User $user;
    private string $title;
    private string $text;

    public function __construct(
        UUID    $uuid,
        User   $user,
        string $title,
        string $text

    )
    {
        $this->uuid=$uuid;
        $this->text=$text;
        $this->user=$user;
        $this->title=$title;

    }

    /**
     * @return UUID
     */
    public function getUuid(): UUID
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
    public function getUser(): User
    {
        return $this->user;
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