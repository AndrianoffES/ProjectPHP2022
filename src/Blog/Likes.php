<?php

namespace project\App\Blog;

use project\App\Users\User;

class Likes
{
    public function __construct(private UUID $uuid,
                                private Post $postUuid,
                                private User $userUuid)
    {
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
    public function getPostUuid(): Post
    {
        return $this->postUuid;
    }

    /**
     * @param Post $postUuid
     */
    public function setPostUuid(Post $postUuid): void
    {
        $this->postUuid = $postUuid;
    }

    /**
     * @return User
     */
    public function getUserUuid(): User
    {
        return $this->userUuid;
    }

    /**
     * @param User $userUuid
     */
    public function setUserUuid(User $userUuid): void
    {
        $this->userUuid = $userUuid;
    }



}