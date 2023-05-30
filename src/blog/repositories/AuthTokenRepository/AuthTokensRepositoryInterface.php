<?php

namespace Habr\Renat\Blog\Repositories\AuthTokenRepository;

use Habr\Renat\Blog\AuthToken;

interface AuthTokensRepositoryInterface {

// Метод сохранения токена
    public function save(AuthToken $authToken): void;

// Метод получения токена
    public function get(string $token): AuthToken;
}