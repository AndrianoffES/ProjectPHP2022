<?php

namespace project\App\Blog\Repositories\AuthTokensRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use PDO;
use PDOException;
use project\App\Blog\AuthToken;
use project\App\Blog\Exceptions\AuthTokenNotFoundException;
use project\App\Blog\Exceptions\AuthTokensRepositoryException;
use project\App\Blog\UUID;

class SqliteAuthTokensRepository implements AuthTokensRepositoryInterface
{
    public function __construct( private PDO $connection
    ){
    }

    /**
     * @throws AuthTokensRepositoryException
     */
    public function save(AuthToken $authToken): void {
        $query =
    'INSERT INTO tokens (
        token, 
        user_uuid, 
        expires_on
    ) VALUES ( 
        :token,
        :user_uuid,
        :expires_on )
    ON CONFLICT (token) DO UPDATE SET expires_on = :expires_on';

    try {
    $statement = $this->connection->prepare($query); 
    $statement->execute([
    ':token' => $authToken->token(),
    ':user_uuid' => (string)$authToken->userUuid(), 
    ':expires_on' => $authToken->expiresOn()->format(DateTimeInterface::ATOM),
    ]);
    } catch (PDOException $e) {
    throw new AuthTokensRepositoryException( 
            $e->getMessage(),
            (int)$e->getCode(),
            $e
            );
        }
    }

    /**
     * @throws AuthTokensRepositoryException
     */
    public function get(string $token): AuthToken {
        try {
            $statement = $this->connection->prepare(
                'SELECT * FROM tokens WHERE token = ?'
            );
            $statement->execute([$token]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(),
                (int)$e->getCode(),
                $e
            );
        }
        if (false === $result) {
            throw new AuthTokenNotFoundException("Cannot find token: $token");
        }
        try {
            return new AuthToken(
                $result['token'],
                new UUID($result['user_uuid']),
                new DateTimeImmutable($result['expires_on'])
            );
        } catch (Exception $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(),
                $e->getCode(),
                $e
            ); }
    }

}