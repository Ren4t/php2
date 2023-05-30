<?php

namespace Habr\Renat\Http\Auth;

use DateTimeImmutable;
use Habr\Renat\Blog\Exceptions\AuthException;
use Habr\Renat\Blog\Exceptions\AuthTokenNotFoundException;
use Habr\Renat\Blog\Exceptions\HttpException;
use Habr\Renat\Blog\Repositories\AuthTokenRepository\AuthTokensRepositoryInterface;
use Habr\Renat\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Habr\Renat\Blog\User;
use Habr\Renat\Http\Request;
use function mb_substr;
use function str_starts_with;

class BearerTokenAuthentication implements TokenAuthenticationInterface {

    private const HEADER_PREFIX = 'Bearer ';

    public function __construct(
// Репозиторий токенов
            private AuthTokensRepositoryInterface $authTokensRepository,
// Репозиторий пользователей
            private UsersRepositoryInterface $usersRepository,
    ) {
        
    }

    public function user(Request $request): User {
        // Получаем HTTP-заголовок
        try {
            $header = $request->header('Authorization');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        // Проверяем, что заголовок имеет правильный формат
        if (!str_starts_with($header, self::HEADER_PREFIX)) {
            throw new AuthException("Malformed token: [$header]");
        }
        // Отрезаем префикс Bearer
        $token = mb_substr($header, strlen(self::HEADER_PREFIX));
        // Ищем токен в репозитории
        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException) {
            throw new AuthException("Bad token: [$token]");
        }
        // Проверяем срок годности токена
        if ($authToken->expiresOn() <= new DateTimeImmutable()) {
            throw new AuthException("Token expired: [$token]");
        }
// Получаем UUID пользователя из токена
        $userUuid = $authToken->userUuid();
// Ищем и возвращаем пользователя
        return $this->usersRepository->get($userUuid);
    }

}
