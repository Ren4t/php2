<?php

use Habr\Renat\blog\User as MyUser;
use Habr\Renat\person\{
    Name,
    Person
};
use Habr\Renat\blog\{
    Post,
    Comment
};
use Habr\Renat\blog\repositories\InMemoryUserRepository;
use Habr\Renat\blog\exceptions\UserNotFoundException;

include __DIR__ . "/vendor/autoload.php";

$faker = Faker\Factory::create("ru_RU");

//echo $faker->name() . PHP_EOL;
//echo $faker->realText(rand(100, 200)) . PHP_EOL;

//spl_autoload_register(function ($class) {
//   
//    $file = "$class.php";
//    $file = str_replace("Habr\\Renat\\", "src/", $file);
//    $file = str_replace("\\", "/", $file);
//    if (file_exists($file)) {
//        include $file;
//    }
//});
$name = new Name('Petr', 'Sidorov');
$user = new MyUser(1, $name, 'admin');
$person = new Person($name, new DateTimeImmutable());
//var_dump($argv[2]);
switch ($argv[1]){
    
}
$post = new Post(
        1,
        $person,
        'Моя статья',
        $faker->realText(rand(100, 200))
);

//echo $post;


$name2 = new Name('Ivan', 'Petrov');
$user2 = new MyUser(2, $name2, 'user');

$comment = new Comment(1, $user2, $post, "всё ок");
//echo $comment;
try {
    $str = $argv[1];
    echo $$str;
} catch (\Habr\Renat\blog\exceptions\AppException $ex) {
    echo $ex->getMessage();
}
//$userRepository = new InMemoryUserRepository();
//
//$userRepository->save($user);
//$userRepository->save($user2);
//try {
//    echo $userRepository->get(1);
//    echo $userRepository->get(2);
//    echo $userRepository->get(3);
//} catch (UserNotFoundException $ex) {
//    echo $ex->getMessage();
//} catch (Exception $ex) {
//    echo $ex->getMessage();
//}
//function foo(string $str): void{
//    switch ($str){ 
//        case "user":
//            echo new User('Vlad','Ivanov');
//            break;
//        case "post":
//            
//        default :
//            echo " не подходящего аргумента";
//}
//}
