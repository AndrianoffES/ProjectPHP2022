<?php

namespace project\App\Blog\Exceptions;

class PostNotFoundException extends \Exception
{

    /**
     * @param string $string
     */
    public function __construct(string $string)
    {
    }
}