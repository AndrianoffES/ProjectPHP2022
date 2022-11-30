<?php

namespace project\App\Http\Auth;

use project\App\Http\Request;
use project\App\Users\User;

interface AuthenticationInterface
{
    public function user(Request $request): User;
}