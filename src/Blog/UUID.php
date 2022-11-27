<?php

namespace project\App\Blog;

use project\App\Blog\Exceptions\InvalidArgumentException;


class UUID
{
    // Внутри объекта мы храним UUID как строку
    private string $uuidString;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $uuidString)
    {
        $this->uuidString = $uuidString;
        if (!uuid_is_valid($uuidString)) {
            throw new InvalidArgumentException( "Malformed UUID: $this->uuidString");
        }
    }

    public static function random(): self
        {
        return new self(uuid_create(UUID_TYPE_RANDOM));
        }
    public function __toString(): string
        {
        return $this->uuidString;
        }
}