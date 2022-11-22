<?php

namespace project\App\Blog\Repositories\LikesRepository;

use PDO;
use project\App\Blog\Likes;
use project\App\Blog\Post;
use project\App\Blog\UUID;


interface LikesRepositoryInterface
{
    public function save(Likes $likes):void;
    public function getByTargetUuid( UUID $uuid): array;
}