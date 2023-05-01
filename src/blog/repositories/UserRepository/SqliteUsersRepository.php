<?php

namespace Habr\Renat\Blog\Repositories\UserRepository;

use Habr\Renat\Blog\User;
use Habr\Renat\Blog\UUID;
use Habr\Renat\Person\Name;
use Habr\Renat\Blog\Exceptions\UserNotFoundException;
use \PDO;
use \PDOStatement;

class SqliteUsersRepository implements UsersRepositoryInterface {

    public function __construct(
            private PDO $connection
    ) {
        
    }

    public function save(User $user): void {
        //Подготавливаем запрос
        $statement = $this->connection->prepare(
                "INSERT INTO users (first_name, last_name, uuid, username) VALUES (:first_name, :last_name, :uuid, :username)"
        );
// Выполняем запрос с конкретными значениями
        $statement->execute([
            ':first_name' => $user->name()->first(),
            ':last_name' => $user->name()->last(),
            ':uuid' => $user->uuid(),
            ':username' => $user->username()
        ]);
    }

    public function get(UUID $uuid): User {
        $statement = $this->connection->prepare(
                'SELECT * FROM users WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string) $uuid,
        ]);
        return $this->getUser($statement, $uuid);
    }

    public function getByUsername(string $username): User {
        $statement = $this->connection->prepare(
                'SELECT * FROM users WHERE username = :username'
        );
        $statement->execute([
            ':username' => $username,
        ]);
        return $this->getUser($statement, $username);
    }

    public function getUser(PDOStatement $statement, string $errorString): User {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
// Бросаем исключение, если пользователь не найден
        if ($result === false) {
            throw new UserNotFoundException(
                            "Cannot get user: $errorString"
            );
        }
        return new User(
                new UUID($result['uuid']),
                new Name($result['first_name'], $result['last_name']),
                $result['username']
        );
    }

}
