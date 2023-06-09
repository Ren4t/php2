<?php

namespace Habr\Renat\UnitTests\Blog\Commands;

use Habr\Renat\Blog\Commands\Arguments;
use Habr\Renat\Blog\Commands\CreateUserCommand;
use Habr\Renat\Blog\Exceptions\ArgumentsException;
use Habr\Renat\Blog\Exceptions\CommandException;
use Habr\Renat\Blog\Exceptions\UserNotFoundException;
use Habr\Renat\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Habr\Renat\Blog\User;
use Habr\Renat\Blog\UUID;
use Habr\Renat\Person\Name;
use Habr\Renat\UnitTests\DummyLogger;
use PHPUnit\Framework\TestCase;

class CreateUserCommandTest extends TestCase {

// Функция возвращает объект типа UsersRepositoryInterface
    private function makeUsersRepository(): UsersRepositoryInterface {
        return new class implements UsersRepositoryInterface {

            public function save(User $user): void {
                
            }

            public function get(UUID $uuid): User {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User {
                throw new UserNotFoundException("Not found");
            }
        };
    }

// Проверяем, что команда создания пользователя бросает исключение,
// если пользователь с таким именем уже существует
    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void {

        $userRepository = new class implements UsersRepositoryInterface {

            public function save(User $user): void {
                
            }

            public function get(UUID $uuid): User {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User {
                return new User(UUID::random(), new Name("first", "last"), "user123");
            }
        };
// Создаём объект команды
// У команды одна зависимость - UsersRepositoryInterface

        $command = new CreateUserCommand(
                $userRepository,
                new DummyLogger()
        );
// Описываем тип ожидаемого исключения
        $this->expectException(CommandException::class);
// и его сообщение
        $this->expectExceptionMessage('User already exists: Ivan');
// Запускаем команду с аргументами
        $command->handle(new Arguments(['username' => 'Ivan']));
    }

    public function testItRequiresLastName(): void {
// Передаём в конструктор команды объект, возвращаемый нашей функцией
        $command = new CreateUserCommand($this->makeUsersRepository(), new DummyLogger());
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: last_name');
        $command->handle(new Arguments([
                    'username' => 'Ivan',
// Нам нужно передать имя пользователя,
// чтобы дойти до проверки наличия фамилии
                    'first_name' => 'Ivan',
        ]));
    }

// Тест проверяет, что команда действительно требует имя пользователя
    public function testItRequiresFirstName(): void {
// Вызываем ту же функцию
        $command = new CreateUserCommand($this->makeUsersRepository(), new DummyLogger());
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: first_name');
        $command->handle(new Arguments(['username' => 'Ivan']));
    }

    // Тест, проверяющий, что команда сохраняет пользователя в репозитории
    public function testItSavesUserToRepository(): void {
// Создаём объект анонимного класса
        $usersRepository = new class implements UsersRepositoryInterface {

// В этом свойстве мы храним информацию о том,
// был ли вызван метод save
            private bool $called = false;

            public function save(User $user): void {
// Запоминаем, что метод save был вызван
                $this->called = true;
            }

            public function get(UUID $uuid): User {

                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User {
                throw new UserNotFoundException("Not found");
            }

// Этого метода нет в контракте UsersRepositoryInterface,
// но ничто не мешает его добавить.
// С помощью этого метода мы можем узнать,
// был ли вызван метод save
            public function wasCalled(): bool {
                return $this->called;
            }
        };
// Передаём наш мок в команду
        $command = new CreateUserCommand($usersRepository, new DummyLogger());
// Запускаем команду
        $command->handle(new Arguments([
                    'username' => 'Ivan',
                    'first_name' => 'Ivan',
                    'last_name' => 'Nikitin',
        ]));
// Проверяем утверждение относительно мока,
// а не утверждение относительно команды
        $this->assertTrue($usersRepository->wasCalled());
    }

    public function tecreatestItRequiresPassword() {
        $command = new CreateUserCommand(
                $this->makeUsersRepository(),
                new DummyLogger()
        );
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: password');
        $command->handle(new Arguments([
                    'username' => 'Ivan',
        ]));
    }

}
