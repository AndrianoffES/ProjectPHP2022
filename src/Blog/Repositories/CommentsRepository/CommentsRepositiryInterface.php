<?php

namespace project\App\Blog\Repositories\CommentsRepository;

use project\App\Blog\Comment;
use project\App\Blog\UUID;

interface CommentsRepositiryInterface
{
    public function save(Comment $comment):void;
    public function get(UUID $uuid):Comment;
}