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
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

// Подключаем автозагрузчик Composer
require_once __DIR__ . '/vendor/autoload.php';
// Создаём объект контейнера ..
$container = new DIContainer();
// .. и настраиваем его:
// Добавляем логгер в контейнер
$container->bind(
// С контрактом логгера из PSR-3 ..
        LoggerInterface::class,
// .. ассоциируем объект логгера из библиотеки monolog
        (new Logger('blog')) // blog – это (произвольное) имя логгера
// Настраиваем логгер так,
// чтобы записи сохранялись в файл
                ->pushHandler(new StreamHandler(
                                __DIR__ . '/logs/blog.log' // Путь до этого файла
                ))
                ->pushHandler(new StreamHandler(
// записывать в файл "blog.error.log"
                                __DIR__ . '/logs/blog.error.log',
// события с уровнем ERROR и выше,
                                level: Logger::ERROR,
// при этом событие не должно "всплывать"
                                bubble: false, //он говорит логгеру, что если 
                                //событие обработано, оно не должно передаваться 
                                //следующим обработчикам.
                ))
                // Добавили ещё один обработчик;
// он будет вызываться первым …
                ->pushHandler(
// .. и вести запись в поток php://stdout,
// то есть в консоль
                        new StreamHandler("php://stdout")
                )
);

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
