<?php

namespace Habr\Renat\Blog\Commands;

use Habr\Renat\Blog\Exceptions\CommandException;
use Habr\Renat\Blog\Exceptions\UserNotFoundException;
use Habr\Renat\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Habr\Renat\Blog\User;
use Habr\Renat\Blog\UUID;
use Habr\Renat\Person\Name;
use Psr\Log\LoggerInterface;

class CreateUserCommand {

    public function __construct(
            private UsersRepositoryInterface $usersRepository,
// Добавили зависимость от логгера
            private LoggerInterface $logger
    ) {
        
    }

    public function handle(Arguments $arguments): void {

        // Логируем информацию о том, что команда запущена
// Уровень логирования – INFO
        $this->logger->info("Create user command started");

        $username = $arguments->get('username');
// Проверяем, существует ли пользователь в репозитории
        if ($this->userExists($username)) {
            // Логируем сообщение с уровнем WARNING
            $this->logger->warning("User already exists: $username");
            throw new CommandException("User already exists: $username");
// Вместо выбрасывания исключения просто выходим из функции
           // return;
// Бросаем исключение, если пользователь уже существует
            //           throw new CommandException("User already exists: $username");
        }
        $uuid = UUID::random();
// Сохраняем пользователя в репозиторий
        $this->usersRepository->save(new User(
                        $uuid,
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
