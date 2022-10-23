<?php

namespace project\App\Blog;

use project\App\Users\User;

class Comment
{
     private UUID $uuid;
     private Post $postUUID;
     private User $authorUUID;
     private string $text;

     public function __construct(
         UUID $uuid,
         Post $postUUID,
         User $authorUUID,
         string $comment
     )
     {
         $this->uuid=$uuid;
         $this->postUUID=$postUUID;
         $this->authorUUID=$authorUUID;
         $this->text=$comment;
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
     * @return Post
     */
    public function getPostUUID(): Post
    {
        return $this->postUUID;
    }

    /**
     * @param Post $postUUID
     */
    public function setPostUUID(Post $postUUID): void
    {
        $this->postUUID = $postUUID;
    }

    /**
     * @return Post
     */
    public function getAuthorUUID(): User
    {
        return $this->authorUUID;
    }

    /**
     * @param Post $authorUUID
     */
    public function setAuthorUUID(Post $authorUUID): void
    {
        $this->authorUUID = $authorUUID;
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


    public function __toString(){
        return 'author with id ' . $this->authorUUID . 'commenting post with id ' . $this->postUUID . 'and speaking' . $this->text;
    }
}