<?php

namespace Habr\Renat\Blog\Commands;

use Habr\Renat\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Habr\Renat\Blog\Exceptions\UserNotFoundException;
use Habr\Renat\Blog\Exceptions\CommandException;
use Habr\Renat\Blog\User;
use Habr\Renat\Blog\UUID;
use Habr\Renat\Person\Name;

class CreateUserCommand {

    public function __construct(
            private UsersRepositoryInterface $usersRepository
    ) {
        
    }

    public function handle(Arguments $arguments): void {
        
        $username = $arguments->get('username');
// Проверяем, существует ли пользователь в репозитории
        if ($this->userExists($username)) {
// Бросаем исключение, если пользователь уже существует
            throw new CommandException("User already exists: $username");
        }
// Сохраняем пользователя в репозиторий
        $this->usersRepository->save(new User(
                        UUID::random(),
                        new Name($arguments->get('first_name'), $arguments->get('last_name')),
                        $username
        ));
    }

    private function userExists(string $username): bool {
        try {
// Пытаемся получить пользователя из репозитория
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }

}
