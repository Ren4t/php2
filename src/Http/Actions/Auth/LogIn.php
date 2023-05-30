<?php

namespace Habr\Renat\Http\Actions\Auth;

use DateTimeImmutable;
use Habr\Renat\Blog\AuthToken;
use Habr\Renat\Blog\Exceptions\AuthException;
use Habr\Renat\Blog\Repositories\AuthTokenRepository\AuthTokensRepositoryInterface;
use Habr\Renat\Http\Actions\ActionInterface;
use Habr\Renat\Http\Auth\PasswordAuthenticationInterface;
use Habr\Renat\Http\ErrorResponse;
use Habr\Renat\Http\Request;
use Habr\Renat\Http\Response;
use Habr\Renat\Http\SuccessfulResponse;

class LogIn implements ActionInterface {

    public function __construct(
// Авторизация по паролю
            private PasswordAuthenticationInterface $passwordAuthentication,
// Репозиторий токенов
            private AuthTokensRepositoryInterface $authTokensRepository
    ) {
        
    }

    public function handle(Request $request): Response {
        // Аутентифицируем пользователя
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
        // Генерируем токен
        $authToken = new AuthToken(
// Случайная строка длиной 40 символов
                bin2hex(random_bytes(40)),
                $user->uuid(),
// Срок годности - 1 день
                (new DateTimeImmutable())->modify('+1 day')
        );
// Сохраняем токен в репозиторий
        $this->authTokensRepository->save($authToken);
// Возвращаем токен
        return new SuccessfulResponse([
            'token' => (string) $authToken,
        ]);
    }

}
