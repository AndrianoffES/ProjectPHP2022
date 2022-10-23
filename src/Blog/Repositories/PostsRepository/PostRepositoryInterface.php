<?php

namespace project\App\Blog\Repositories\PostsRepository;

use project\App\Blog\Post;
use project\App\Blog\UUID;
use project\App\Users\User;

interface PostRepositoryInterface
{
public function save(Post $post);
public function get(UUID $uuid);
}