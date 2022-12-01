<?php

namespace project\App\Http\Action;

use DateTimeImmutable;
use project\App\Blog\Exceptions\AuthException;
use project\App\Blog\Repositories\AuthTokensRepository\SqliteAuthTokensRepository;
use project\App\Http\Auth\BearerTokenAuthentication;
use project\App\Http\Request;
use project\App\Http\Response;
use project\App\Http\SuccessfulResponse;

class LogOut implements ActionInterface
{
    public function __construct(
        private SqliteAuthTokensRepository $authTokensRepository,
        private BearerTokenAuthentication $bearerTokenAuthentication
    )
    {
    }

    /**
     * @param Request $request
     * @return Response
     * @throws AuthException
     */
    public function handle(Request $request): Response
    {
        $currentToken=$this->bearerTokenAuthentication->getAuthTokenString($request);

        try{
            $authToken = $this->authTokensRepository->get($currentToken);
        }catch (AuthException $e) {
            throw new AuthException($e->getMessage());
        }

        $authToken->setExpiresOn((new DateTimeImmutable())->modify('now') );
        $this->authTokensRepository->save($authToken);
        return new SuccessfulResponse([ 'token' => (string)$authToken->token(),
        ]);

    }
}