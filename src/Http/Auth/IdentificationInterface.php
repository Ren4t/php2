<?php

namespace Habr\Renat\Http\Auth;

use Habr\Renat\Blog\User;
use Habr\Renat\Http\Request;


interface IdentificationInterface {
    // Контракт описывает единственный метод,
// получающий пользователя из запроса
    public function user(Request $request): User;
}
