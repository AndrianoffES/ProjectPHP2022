<?php

namespace project\App\Blog;

use project\App\Users\User;

class Comment
{
     private int $id;
     private Post $text;
     private User $user;
     private string $comment;

     public function __construct(
         int $id,
         Post $text,
         User $user,
         string $comment
     )
     {
         $this->id=$id;
         $this->user=$user;
         $this->text=$text;
         $this->comment=$comment;
     }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Post
     */
    public function getText(): Post
    {
        return $this->text;
    }

    /**
     * @param Post $text
     */
    public function setText(Post $text): void
    {
        $this->text = $text;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function __toString(){
        return $this->user . 'commenting' . $this->text . 'and speaking' . $this->comment;
    }
}