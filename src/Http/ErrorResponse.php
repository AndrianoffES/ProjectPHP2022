<?php

namespace project\App\Http;

class ErrorResponse extends Response
{
    protected const SUCCESS = false;
// Неуспешный ответ содержит строку с причиной неуспеха, // по умолчанию - 'Something goes wrong'
    public function __construct(
        private string $reason = 'Something goes wrong' ){
    }
// Реализация абстрактного метода // родительского класса
    protected function payload(): array {
        return ['reason' => $this->reason];
    }
}