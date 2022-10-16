<?php

namespace project\App\Blog;


use project\App\Users\Name;
use project\App\Users\User;
class Post{
    private int $id;
    private User $user;
    private string $text;

    public function __construct(
        int    $id,
        User   $user,
        string $text

    )
    {
        $this->id=$id;
        $this->text=$text;
        $this->user=$user;

    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Name
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param Name $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
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
        return $this->user . 'writing ' . $this->text;
    }

}