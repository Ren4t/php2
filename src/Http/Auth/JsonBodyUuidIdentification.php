<?php

namespace Habr\Renat\Http\Auth;

use Habr\Renat\Blog\Exceptions\AuthException;
use Habr\Renat\Blog\Exceptions\HttpException;
use Habr\Renat\Blog\Exceptions\InvalidArgumentException;
use Habr\Renat\Blog\Exceptions\UserNotFoundException;
use Habr\Renat\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Habr\Renat\Blog\User;
use Habr\Renat\Blog\UUID;
use Habr\Renat\Http\Request;

class JsonBodyUuidIdentification implements IdentificationInterface {

    public function __construct(
            private UsersRepositoryInterface $usersRepository
    ) {
        
    }

    public function user(Request $request): User {
        try {
// Получаем UUID пользователя из JSON-тела запроса;
// ожидаем, что корректный UUID находится в поле user_uuid
            $userUuid = new UUID($request->jsonBodyField('user_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
// Если невозможно получить UUID из запроса -
// бросаем исключение
            throw new AuthException($e->getMessage());
        }
        try {
// Ищем пользователя в репозитории и возвращаем его
            return $this->usersRepository->get($userUuid);
        } catch (UserNotFoundException $e) {
// Если пользователь с таким UUID не найден -
// бросаем исключение
            throw new AuthException($e->getMessage());
        }
    }

}
