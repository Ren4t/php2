<?php

namespace Habr\Renat\UnitTests\Blog\Commands\Users;

use Habr\Renat\Blog\Commands\Users\CreateUser;
use Habr\Renat\Blog\Exceptions\UserNotFoundException;
use Habr\Renat\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Habr\Renat\Blog\User;
use Habr\Renat\Blog\UUID;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class CreateUserCommandSymfonyTest extends TestCase{

    public function testItRequiresLastName(): void {
// Тестируем новую команду
        $command = new CreateUser(
                $this->makeUsersRepository(),
        );
// Меняем тип ожидаемого исключения ..
        $this->expectException(RuntimeException::class);
// .. и его сообщение
        $this->expectExceptionMessage(
                'Not enough arguments (missing: "last_name").'
        );
// Запускаем команду методом run вместо handle
        $command->run(
// Передаём аргументы как ArrayInput,
// а не Arguments
// Сами аргументы не меняются
                new ArrayInput([
                    'username' => 'Ivan',
                    'password' => 'some_password',
                    'first_name' => 'Ivan',
                        ]),
// Передаём также объект,
// реализующий контракт OutputInterface
// Нам подойдёт реализация,
// которая ничего не делает
                new NullOutput()
        );
    }

    public function testItRequiresPassword(): void {
        $command = new CreateUser(
                $this->makeUsersRepository()
        );
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
                'Not enough arguments (missing: "first_name, last_name, password"'
        );
        $command->run(
                new ArrayInput([
                    'username' => 'Ivan',
                        ]),
                new NullOutput()
        );
    }

    public function testItRequiresFirstName(): void {
        $command = new CreateUser(
                $this->makeUsersRepository()
        );
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
                'Not enough arguments (missing: "first_name, last_name").'
        );
        $command->run(
                new ArrayInput([
                    'username' => 'Ivan',
                    'password' => 'some_password',
                        ]),
                new NullOutput()
        );
    }

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
        $command = new CreateUser(
                $usersRepository
        );
        $command->run(
                new ArrayInput([
                    'username' => 'Ivan',
                    'password' => 'some_password',
                    'first_name' => 'Ivan',
                    'last_name' => 'Nikitin',
                        ]),
                new NullOutput()
        );
        $this->assertTrue($usersRepository->wasCalled());
    }

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

}
