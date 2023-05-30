<?php

namespace Habr\Renat\Blog\Repositories\PostRepository;

use Habr\Renat\Blog\Exceptions\PostNotFoundException;
use Habr\Renat\Blog\Exceptions\PostsRepositoryException;
use Habr\Renat\Blog\Post;
use Habr\Renat\Blog\Repositories\PostRepository\PostsRepositoryInterface;
use Habr\Renat\Blog\Repositories\UserRepository\SqliteUsersRepository;
use Habr\Renat\Blog\UUID;
use PDO;
use PDOException;
use PDOStatement;
use Psr\Log\LoggerInterface;

class SqlitePostsRepository implements PostsRepositoryInterface {

    public function __construct(
            private PDO $connection,
            private LoggerInterface $logger
    ) {
        
    }

    public function save(Post $post): void {
        //Подготавливаем запрос
        $statement = $this->connection->prepare(
                "INSERT INTO posts (uuid, author_uuid, title, text) VALUES (:uuid, :author_uuid, :title, :text)"
        );

        $uuid = $post->uuid();
// Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => $uuid,
            ':author_uuid' => $post->user()->uuid(),
            ':title' => $post->title(),
            ':text' => $post->text()
        ]);

        $this->logger->info("Save post $uuid");
    }

    public function get(UUID $uuid): Post {
        $statement = $this->connection->prepare(
                'SELECT * FROM posts WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string) $uuid,
        ]);
        return $this->getPost($statement, $uuid);
    }

    public function getPost(PDOStatement $statement, string $uuid): Post {

        $result = $statement->fetch(PDO::FETCH_ASSOC);
// Бросаем исключение, если пользователь не найден
        if ($result === false) {
            $message = "Cannot found post: $uuid";
            $this->logger->warning($message);
            throw new PostNotFoundException($message);
        }

        $usersRepository = new SqliteUsersRepository($this->connection, $this->logger);
        return new Post(
                new UUID($result['uuid']),
                $usersRepository->get(new UUID($result['author_uuid'])),
                $result['title'],
                $result['text']
        );
    }

    public function delete(UUID $uuid) : void{

        try {
            $statement = $this->connection->prepare(
                    'DELETE FROM posts WHERE uuid = ?'
            );
            $statement->execute([(string) $uuid]);
        } catch (PDOException $e) {
            throw new PostsRepositoryException(
                            $e->getMessage(), (int) $e->getCode(), $e
            );
        }
    }

}
