<?php

use Habr\Renat\Blog\Repositories\UserRepository\SqliteUsersRepository;
use Habr\Renat\Blog\Repositories\UserRepository\InMemoryUserRepository;
use Habr\Renat\Blog\Exceptions\AppException;
use Habr\Renat\Blog\Commands\CreateUserCommand;
use Habr\Renat\Blog\Commands\Arguments;
use Habr\Renat\Blog\Repositories\CommentRepository\SqliteCommentsRepository;
use Habr\Renat\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Habr\Renat\Blog\Post;
use Habr\Renat\Blog\Comment;
use Habr\Renat\Blog\User;
use Habr\Renat\Blog\UUID;
use Habr\Renat\Person\Name;

include __DIR__ . "/vendor/autoload.php";

$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$faker = Faker\Factory::create('ru_RU');

$userRepository = new SqliteUsersRepository($connection);


try{
    echo $userRepository->getByUsername('ivan');
} catch (Exception $ex) {

}

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
