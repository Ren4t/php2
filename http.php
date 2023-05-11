<?php

use Habr\Renat\Blog\Exceptions\AppException;
use Habr\Renat\Blog\Exceptions\HttpException;
use Habr\Renat\Blog\Repositories\CommentRepository\SqliteCommentsRepository;
use Habr\Renat\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Habr\Renat\Blog\Repositories\UserRepository\SqliteUsersRepository;
use Habr\Renat\Http\Actions\Posts\CreateComment;
use Habr\Renat\Http\Actions\Posts\CreatePost;
use Habr\Renat\Http\Actions\Posts\FindByUuid;
use Habr\Renat\Http\Actions\Users\CreateUser;
use Habr\Renat\Http\Actions\Users\FindByUsername;
use Habr\Renat\Http\ErrorResponse;
use Habr\Renat\Http\Request;

require_once __DIR__ . '/vendor/autoload.php';

//$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');
//$usersRepository = new SqliteUsersRepository($connection);
//$findByUsername = new FindByUsername($usersRepository);
// Создаём объект запроса из суперглобальных переменных
$request = new Request(
        $_GET,
        $_SERVER,
        file_get_contents('php://input')
);
//var_dump(file_get_contents('php://input'));
//"{
//"author_uuid": "c7b0aada-fdab-4e4a-8c8b-5399956fa670",
//"post_uuid": "dd16aacf-5c43-4511-8f1a-ae5b79f45dd3",
//"text": "some text"
//}
//"
try {
// Пытаемся получить путь из запроса
    $path = $request->path();
} catch (HttpException) {
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
} catch (HttpException) {
// Возвращаем неудачный ответ,
// если по какой-то причине
// не можем получить метод
    (new ErrorResponse)->send();
    return;
}


$routes = ['GET' => [
// Создаём действие, соответствующее пути /users/show
        '/users/show' => new FindByUsername(
// Действию нужен репозиторий
                new SqliteUsersRepository(
// Репозиторию нужно подключение к БД
                        new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
                )
        ),
// Второй маршрут
        '/posts/show' => new FindByUuid(
                new SqlitePostsRepository(
                        new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
                )
        ),
    ],
    'POST' => [
        '/posts/create' => new CreatePost(
                new SqlitePostsRepository(
                        new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
                ),
                new SqliteUsersRepository(
                        new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
                )
        ),
        '/posts/comment' => new CreateComment(
                new SqliteCommentsRepository(
                        new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
                ),
                new SqlitePostsRepository(
                        new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
                ),
                new SqliteUsersRepository(
                        new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
                )
        ),
        '/users/create' => new CreateUser(
// Действию нужен репозиторий
                new SqliteUsersRepository(
// Репозиторию нужно подключение к БД
                        new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
                )
        ),
    ]
];
// Если у нас нет маршрута для пути из запроса -
// отправляем неуспешный ответ
if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Not found'))->send();
    return;
}

// Выбираем найденное действие
$action = $routes[$method][$path];

try {
// Пытаемся выполнить действие,
// при этом результатом может быть
// как успешный, так и неуспешный ответ
    $response = $action->handle($request);
    $response->send();
} catch (Exception $e) {
// Отправляем неудачный ответ,
// если что-то пошло не так
    (new ErrorResponse($e->getMessage()))->send();
}

// Отправляем ответ
