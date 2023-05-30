<?php

namespace Habr\Renat\Http\Auth;

use Habr\Renat\Blog\Exceptions\AuthException;
use Habr\Renat\Blog\Exceptions\HttpException;
use Habr\Renat\Blog\Exceptions\UserNotFoundException;
use Habr\Renat\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Habr\Renat\Blog\User;
use Habr\Renat\Http\Request;

class JsonBodyUsernameIdentification implements AuthenticationInterface {

    public function __construct(
            private UsersRepositoryInterface $usersRepository
    ) {
        
    }

    public function user(Request $request): User {
        try {
// Получаем имя пользователя из JSON-тела запроса;
// ожидаем, что имя пользователя находится в поле username
            $username = $request->jsonBodyField('username');
        } catch (HttpException $e) {
// Если невозможно получить имя пользователя из запроса -
// бросаем исключение
            throw new AuthException($e->getMessage());
        }
        try {
// Ищем пользователя в репозитории и возвращаем его
            return $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
// Если пользователь не найден -
// бросаем исключение
            throw new AuthException($e->getMessage());
        }
    }

}
