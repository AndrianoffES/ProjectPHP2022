<?php

namespace project\App\Blog\Repositories\CommentsRepository;

use project\App\Blog\Comment;
use project\App\Blog\Post;

interface CommentsRepositiryInterface
{
    public function save(Comment $comment, Post $uuid);
    public function get(Comment $uuid);
}