<?php

namespace Habr\Renat\Http\Auth;

use Habr\Renat\Blog\Exceptions\AuthException;
use Habr\Renat\Blog\Exceptions\HttpException;
use Habr\Renat\Blog\Exceptions\UserNotFoundException;
use Habr\Renat\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Habr\Renat\Blog\User;
use Habr\Renat\Http\Request;

class PasswordAuthentication implements AuthenticationInterface {

    public function __construct(
            private UsersRepositoryInterface $usersRepository
    ) {
        
    }

    public function user(Request $request): User {
        // 1. Идентифицируем пользователя
        try {
            $username = $request->jsonBodyField('username');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }
        try {
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }
// 2. Аутентифицируем пользователя
// Проверяем, что предъявленный пароль
// соответствует сохранённому в БД

        try {
            $password = $request->jsonBodyField('password');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        // Проверяем пароль методом пользователя
        if (!$user->checkPassword($password)) {
            throw new AuthException('Wrong password');
        }

// Пользователь аутентифицирован
        return $user;
    }

}
