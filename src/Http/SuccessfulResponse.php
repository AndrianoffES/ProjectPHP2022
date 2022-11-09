<?php

namespace project\App\Http;

class SuccessfulResponse extends Response
{
    protected const SUCCESS = true;
// Успешный ответ содержит массив с данными, // по умолчанию - пустой
    public function __construct(
        private array $data = [] ){
    }
// Реализация абстрактного метода // родительского класса
    protected function payload(): array {
        return ['data' => $this->data]; }
}