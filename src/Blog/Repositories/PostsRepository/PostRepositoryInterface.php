<?php

namespace project\App\Blog\Repositories\PostsRepository;

use project\App\Blog\Post;
use project\App\Users\User;

interface PostRepositoryInterface
{
public function save(Post $post, string $uuid);
public function get(Post $uuid);
}