<?php

use project\App\Users\Name;
use project\App\Blog\Post;
use project\App\Users\User;



require_once __DIR__ . '/vendor/autoload.php';

$faker = Faker\Factory::create('ru_RU');

$rout = $argv[1] ?? null;

var_dump($argv);
$name = new Name(
    $faker->firstNameMale(),
    $faker->lastName('male')
);

$user = new User(
    $faker->randomDigitNotNull(),
    $name,
    $faker->sentence(1)
);

switch ($rout){
    case "user":
        echo $user;
        break;
    case "post":
        $post = new Post(
            $faker->randomDigitNotNull,
            $user,
            $faker->realText(100)
        );
        echo $post;
        break;
    case "comment":
        $post = new Post(
            $faker->randomDigitNotNull,
            $user,
            $faker->realText(100)
        );
        $comment = new \project\App\Blog\Comment(
            $faker->randomDigitNotNull,
            $post,
            $user,
            $faker->realText(50)

        );
        echo $comment;
        break;
    default:
        echo 'error try user post comment parameter';
}

