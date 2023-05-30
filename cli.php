<?php

use Habr\Renat\Blog\Commands\FakeData\PopulateDB;
use Habr\Renat\Blog\Commands\Posts\DeletePost;
use Habr\Renat\Blog\Commands\Users\CreateUser;
use Habr\Renat\Blog\Commands\Users\UpdateUser;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;

//php cli.php username=user123 first_name=Ivan last_name=Baraban
// Подключаем файл bootstrap.php
// и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';
// Получаем объект логгера из контейнера
$logger = $container->get(LoggerInterface::class);
// Создаём объект приложения
$application = new Application();

// Перечисляем классы команд
$commandsClasses = [
    CreateUser::class,
    DeletePost::class,
    UpdateUser::class,
    PopulateDB::class,
];

foreach ($commandsClasses as $commandClass) {
// Посредством контейнера
// создаём объект команды
    $command = $container->get($commandClass);
// Добавляем команду к приложению
    $application->add($command);
}
// Запускаем приложение
$application->run();
//try {
//    $command->handle(Arguments::fromArgv($argv));
//} catch (AppException $e) {
//    // Логируем информацию об исключении.
//// Объект исключения передаётся логгеру
//// с ключом "exception".
//// Уровень логирования – ERROR
//    $logger->error($e->getMessage(), ['exception' => $e]);
//}

//include __DIR__ . "/vendor/autoload.php";
//
//$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');
//
//$faker = Faker\Factory::create('ru_RU');
//