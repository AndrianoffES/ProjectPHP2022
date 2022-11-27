<?php

namespace project\App\Blog\Repositories\CommentsRepository;

use project\App\Blog\Comment;
use project\App\Blog\UUID;

interface CommentsRepositoryInterface
{
    public function save(Comment $comment):void;
    public function get(UUID $uuid):Comment;
}