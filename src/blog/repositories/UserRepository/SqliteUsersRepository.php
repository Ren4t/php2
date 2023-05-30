<?php

namespace Habr\Renat\Blog\Repositories\UserRepository;

use Habr\Renat\Blog\Exceptions\UserNotFoundException;
use Habr\Renat\Blog\User;
use Habr\Renat\Blog\UUID;
use Habr\Renat\Person\Name;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class SqliteUsersRepository implements UsersRepositoryInterface {

    public function __construct(
            private PDO $connection,
            private LoggerInterface $logger
    ) {
        
    }

    public function save(User $user): void {
        //Подготавливаем запрос
        $statement = $this->connection->prepare(
                'INSERT INTO users (
uuid,
username,
password,
first_name,
last_name
)
VALUES (
:uuid,
:username,
:password,
:first_name,
:last_name
)
ON CONFLICT (uuid) DO UPDATE SET
first_name = :first_name,
last_name = :last_name'
        );

        $uuid = $user->uuid();
// Выполняем запрос с конкретными значениями
        $statement->execute([
            ':first_name' => $user->name()->first(),
            ':last_name' => $user->name()->last(),
            ':uuid' => $uuid,
            ':username' => $user->username(),
            ':password' => $user->hashedPassword()
        ]);
        $this->logger->info("Save user $uuid");
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

    public function getUser(PDOStatement $statement, string $uuid): User {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
// Бросаем исключение, если пользователь не найден
        if ($result === false) {
            $message = "Cannot found user: $uuid";
            $this->logger->warning($message);
            throw new UserNotFoundException($message);
        }
        return new User(
                new UUID($result['uuid']),
                new Name($result['first_name'], $result['last_name']),
                $result['username'],
                $result['password']
        );
    }

}
