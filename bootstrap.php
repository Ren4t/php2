<?php

use Habr\Renat\Blog\Repositories\CommentRepository\CommentsRepositoryInterface;
use Habr\Renat\Blog\Repositories\CommentRepository\SqliteCommentsRepository;
use Habr\Renat\Blog\Repositories\LikeRepository\LikesRepositoryInterface;
use Habr\Renat\Blog\Repositories\LikeRepository\SqliteLikesRepository;
use Habr\Renat\Blog\Repositories\PostRepository\PostsRepositoryInterface;
use Habr\Renat\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Habr\Renat\Blog\Repositories\UserRepository\SqliteUsersRepository;
use Habr\Renat\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Habr\Renat\Container\DIContainer;

// Подключаем автозагрузчик Composer
require_once __DIR__ . '/vendor/autoload.php';
// Создаём объект контейнера ..
$container = new DIContainer();
// .. и настраиваем его:
// 1. подключение к БД
$container->bind(
        PDO::class,
        new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);
// 2. репозиторий статей
$container->bind(
        PostsRepositoryInterface::class,
        SqlitePostsRepository::class
);
// 3. репозиторий пользователей
$container->bind(
        UsersRepositoryInterface::class,
        SqliteUsersRepository::class
);

$container->bind(
        CommentsRepositoryInterface::class,
        SqliteCommentsRepository::class
);
$container->bind(
        LikesRepositoryInterface::class,
        SqliteLikesRepository::class
);
// Возвращаем объект контейнера
return $container;
