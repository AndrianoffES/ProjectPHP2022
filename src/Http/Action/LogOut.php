<?php

namespace project\App\Http\Action;

use DateTimeImmutable;
use project\App\Blog\Repositories\AuthTokensRepository\SqliteAuthTokensRepository;
use project\App\Http\Request;
use project\App\Http\Response;
use project\App\Http\SuccessfulResponse;

class LogOut implements ActionInterface
{
    public function __construct(private SqliteAuthTokensRepository $authTokensRepository)
    {
    }
    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        $currentToken=$this->authTokensRepository->get($request->jsonBodyField('user_token'));
        $currentToken->setExpiresOn((new DateTimeImmutable())->modify('now') );
        $this->authTokensRepository->save($currentToken);
        return new SuccessfulResponse([ 'token' => (string)$currentToken->token(),
        ]);

    }
}