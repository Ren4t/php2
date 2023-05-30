<?php

use Habr\Renat\Blog\Exceptions\HttpException;
use Habr\Renat\Http\Actions\Auth\LogIn;
use Habr\Renat\Http\Actions\Posts\CreateComment;
use Habr\Renat\Http\Actions\Posts\CreateLike;
use Habr\Renat\Http\Actions\Posts\CreatePost;
use Habr\Renat\Http\Actions\Posts\DeletePost;
use Habr\Renat\Http\Actions\Posts\FindByUuid;
use Habr\Renat\Http\Actions\Users\CreateUser;
use Habr\Renat\Http\Actions\Users\FindByUsername;
use Habr\Renat\Http\ErrorResponse;
use Habr\Renat\Http\Request;
use Psr\Log\LoggerInterface;

// Подключаем файл bootstrap.php
// и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';

// Создаём объект запроса из суперглобальных переменных
$request = new Request(
        $_GET,
        $_SERVER,
        file_get_contents('php://input')
);

// Получаем объект логгера из контейнера
$logger = $container->get(LoggerInterface::class);

try {
// Пытаемся получить путь из запроса
    $path = $request->path();
} catch (HttpException $e) {
    // Логируем сообщение с уровнем WARNING
    $logger->warning($e->getMessage());
// Отправляем неудачный ответ,
// если по какой-то причине
// не можем получить путь
    (new ErrorResponse)->send();
// Выходим из программы
    return;
}

try {
// Пытаемся получить HTTP-метод запроса
    $method = $request->method();
} catch (HttpException $e) {
    // Логируем сообщение с уровнем WARNING
    $logger->warning($e->getMessage());
// Возвращаем неудачный ответ,
// если по какой-то причине
// не можем получить метод
    (new ErrorResponse)->send();
    return;
}


$routes = ['GET' => [
// Создаём действие, соответствующее пути /users/show
        '/users/show' => FindByUsername::class,
// Второй маршрут
        '/posts/show' => FindByUuid::class,
    ],
    'POST' => [
        // Добавили маршрут обмена пароля на токен
        '/login' => LogIn::class,
        '/posts/create' => CreatePost::class,
        '/posts/comment' => CreateComment::class,
        '/users/create' => CreateUser::class,
        '/posts/like' => CreateLike::class,
    ],
    'DELETE' => [
        '/posts' => DeletePost::class
    ]
];

if (!array_key_exists($method, $routes) || !array_key_exists($path, $routes[$method])) {
// Логируем сообщение с уровнем NOTICE
    $message = "Route not found: $method $path";
    $logger->notice($message);
    (new ErrorResponse($message))->send();
    return;
}

// Выбираем найденное действие
$actionClassName = $routes[$method][$path];

try {
    $action = $container->get($actionClassName);
// Пытаемся выполнить действие,
// при этом результатом может быть
// как успешный, так и неуспешный ответ
    $response = $action->handle($request);
    // Отправляем ответ
} catch (Exception $e) {
    // Логируем сообщение с уровнем ERROR
    $logger->error($e->getMessage(), ['exception' => $e]);
// Больше не отправляем пользователю
// конкретное сообщение об ошибке,
// а только логируем его
    (new ErrorResponse($e->getMessage()))->send();
    return;
}

$response->send();
