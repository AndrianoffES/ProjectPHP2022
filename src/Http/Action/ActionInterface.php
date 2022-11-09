<?php

namespace project\App\Http\Action;

use project\App\Http\Request;
use project\App\Http\Response;

interface ActionInterface
{
    public function handle(Request $request): Response;
}