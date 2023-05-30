<?php

use Dotenv\Dotenv;
use Faker\Generator;
use Faker\Provider\Lorem;
use Faker\Provider\ru_RU\Internet;
use Faker\Provider\ru_RU\Person;
use Faker\Provider\ru_RU\Text;
use Habr\Renat\Blog\Repositories\AuthTokenRepository\AuthTokensRepositoryInterface;
use Habr\Renat\Blog\Repositories\AuthTokenRepository\SqliteAuthTokensRepository;
use Habr\Renat\Blog\Repositories\CommentRepository\CommentsRepositoryInterface;
use Habr\Renat\Blog\Repositories\CommentRepository\SqliteCommentsRepository;
use Habr\Renat\Blog\Repositories\LikeRepository\LikesRepositoryInterface;
use Habr\Renat\Blog\Repositories\LikeRepository\SqliteLikesRepository;
use Habr\Renat\Blog\Repositories\PostRepository\PostsRepositoryInterface;
use Habr\Renat\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Habr\Renat\Blog\Repositories\UserRepository\SqliteUsersRepository;
use Habr\Renat\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Habr\Renat\Container\DIContainer;
use Habr\Renat\Http\Auth\BearerTokenAuthentication;
use Habr\Renat\Http\Auth\PasswordAuthentication;
use Habr\Renat\Http\Auth\PasswordAuthenticationInterface;
use Habr\Renat\Http\Auth\TokenAuthenticationInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

// Подключаем автозагрузчик Composer
require_once __DIR__ . '/vendor/autoload.php';
// Загружаем переменные окружения из файла .env
Dotenv::createImmutable(__DIR__)->safeLoad();
// Создаём объект контейнера ..
$container = new DIContainer();
// .. и настраиваем его:
// Добавляем логгер в контейнер
// Выносим объект логгера в переменную
$logger = (new Logger('blog'));
// Включаем логирование в файлы,
// если переменная окружения LOG_TO_FILES
// содержит значение 'yes'
if ($_SERVER['LOG_TO_FILES'] === 'yes') {
    $logger
            ->pushHandler(new StreamHandler(
                            __DIR__ . '/logs/blog.log'
            ))
            ->pushHandler(new StreamHandler(
                            __DIR__ . '/logs/blog.error.log',
                            level: Logger::ERROR,
                            bubble: false,
    ));
}
// Включаем логирование в консоль,
// если переменная окружения LOG_TO_CONSOLE
// содержит значение 'yes'
if ($_SERVER['LOG_TO_CONSOLE'] === 'yes') {
    $logger
            ->pushHandler(
                    new StreamHandler("php://stdout")
    );
}
$container->bind(
        LoggerInterface::class,
        $logger
);

//$container->bind(
//// С контрактом логгера из PSR-3 ..
//        LoggerInterface::class,
//// .. ассоциируем объект логгера из библиотеки monolog
//        (new Logger('blog')) // blog – это (произвольное) имя логгера
//// Настраиваем логгер так,
//// чтобы записи сохранялись в файл
//                ->pushHandler(new StreamHandler(
//                                __DIR__ . '/logs/blog.log' // Путь до этого файла
//                ))
//                ->pushHandler(new StreamHandler(
//// записывать в файл "blog.error.log"
//                                __DIR__ . '/logs/blog.error.log',
//// события с уровнем ERROR и выше,
//                                level: Logger::ERROR,
//// при этом событие не должно "всплывать"
//                                bubble: false, //он говорит логгеру, что если 
//                                //событие обработано, оно не должно передаваться 
//                                //следующим обработчикам.
//                ))
//                // Добавили ещё один обработчик;
//// он будет вызываться первым …
//                ->pushHandler(
//// .. и вести запись в поток php://stdout,
//// то есть в консоль
//                        new StreamHandler("php://stdout")
//                )
//);
//// Создаём объект генератора тестовых данных
$faker = new Generator();

// Инициализируем необходимые нам виды данных
$faker->addProvider(new Person($faker));
$faker->addProvider(new Text($faker));
$faker->addProvider(new Internet($faker));
$faker->addProvider(new Lorem($faker));
// Добавляем генератор тестовых данных
// в контейнер внедрения зависимостей
$container->bind(
        Generator::class,
        $faker
);

// 1. подключение к БД
$container->bind(
        PDO::class,
// Берём путь до файла базы данных SQLite
// из переменной окружения SQLITE_DB_PATH
        new PDO('sqlite:' . __DIR__ . '/' . $_SERVER['SQLITE_DB_PATH'])
//$_ENV или $_SERVER
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
//$container->bind(
//        AuthenticationInterface::class,
//        JsonBodyUuidIdentification::class
//);
$container->bind(
        PasswordAuthenticationInterface::class,
        PasswordAuthentication::class
);
$container->bind(
        AuthTokensRepositoryInterface::class,
        SqliteAuthTokensRepository::class
);
$container->bind(
        TokenAuthenticationInterface::class,
        BearerTokenAuthentication::class
);

// Возвращаем объект контейнера
return $container;
