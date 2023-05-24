<?php

use Habr\Renat\Blog\Commands\Arguments;
use Habr\Renat\Blog\Commands\CreateUserCommand;
use Habr\Renat\Blog\Exceptions\AppException;
use Psr\Log\LoggerInterface;

//php cli.php username=user123 first_name=Ivan last_name=Baraban
// Подключаем файл bootstrap.php
// и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';
// При помощи контейнера создаём команду
$command = $container->get(CreateUserCommand::class);

// Получаем объект логгера из контейнера
$logger = $container->get(LoggerInterface::class);
try {
    $command->handle(Arguments::fromArgv($argv));
} catch (AppException $e) {
    // Логируем информацию об исключении.
// Объект исключения передаётся логгеру
// с ключом "exception".
// Уровень логирования – ERROR
    $logger->error($e->getMessage(), ['exception' => $e]);
}

//include __DIR__ . "/vendor/autoload.php";
//
//$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');
//
//$faker = Faker\Factory::create('ru_RU');
//
//$userRepository = new SqliteUsersRepository($connection);
//
//
//try{
//    echo $userRepository->getByUsername('ivan');
//} catch (Exception $ex) {
//
//}

//$likesRepository = new SqliteLikesRepository($connection);
//$postRepository = new SqlitePostsRepository($connection);
//$post = $postRepository->get(new UUID('09451416-c5e6-48e3-8872-c498630170d0'));
//$userRepository = new SqliteUsersRepository($connection);
//try{
//    $user1= $userRepository->getByUsername('ivan');
//    $user2= $userRepository->getByUsername('ivan44');
//} catch (Exception $ex) {
//
//}
//$likesRepository->save(new Like(
//        UUID::random(),
//        $post,
//        $user2
//));

//print_r($likesRepository->getByPostUuid(new UUID('09451416-c5e6-48e3-8872-c498630170d0')));

//$commentRepository = new SqlCommentRepository($connection);
//$postRepository = new SqlitePostRepository($connection);
//$userRepository = new SqliteUsersRepository($connection);
//try {
//
//    $user1 = $userRepository->getByUsername('lidiy.makarova');
//
//    $post1 = $postRepository->get(new UUID('f6f3139f-ae84-4797-b286-9bddaf02e11c'));
//
//    $postRepository->save(
//            new Post(
//                    UUID::random(),
//                    $user1,
//                    $faker->word(),
//                    $faker->realText(100)
//            )
//    );
//
//    $commentRepository->save(
//            new Comment(
//                    UUID::random(),
//                    $user1,
//                    $post1,
//                    $faker->realText(50)
//            )
//    );
//} catch (Exception $ex) {
//    echo $ex->getMessage();
//}
//
//try {
//    var_dump($commentRepository->get(new UUID('c3f85f5e-5168-48bf-ab5a-914ae261c957')));
//} catch (Exception $ex) {
//    
//}
//$usersRepository = new SqliteUsersRepository($connection);
//
//$command = new CreateUserCommand($usersRepository);
//try {
//// Запускаем команду
//    $command->handle(Arguments::fromArgv($argv));
//} catch (AppException $e) {
//// Выводим сообщения об ошибках
//    echo "{$e->getMessage()}\n";
//}
//
//try {
//    echo $usersRepository->getByUsername("lidiy.makarova");
//} catch (Exception $exc) {
//    var_dump($exc->getTrace());
//    echo $exc->getMessage();
//}
