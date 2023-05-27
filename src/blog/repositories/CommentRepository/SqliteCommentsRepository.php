<?php

namespace Habr\Renat\Blog\Repositories\CommentRepository;

use Habr\Renat\Blog\Comment;
use Habr\Renat\Blog\Exceptions\CommentNotFoundException;
use Habr\Renat\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Habr\Renat\Blog\Repositories\UserRepository\SqliteUsersRepository;
use Habr\Renat\Blog\UUID;
use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;

class SqliteCommentsRepository implements CommentsRepositoryInterface{
    
    public function __construct(
            private PDO $connection,
            private LoggerInterface $logger
    ) {
        
    }

    public function save(Comment $comment): void {
        $statement = $this->connection->prepare(
                "INSERT INTO comments (uuid, post_uuid, author_uuid, text) VALUES (:uuid, :post_uuid, :author_uuid, :text)"
        );
        $uuid = $comment->uuid();
// Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => $uuid,
            ':post_uuid' => $comment->post()->uuid(),
            ':author_uuid' => $comment->user()->uuid(),
            ':text' => $comment->text()
        ]);
        $this->logger->info("Save comment $uuid");
    }
    
    public function get(UUID $uuid): Comment {
         $statement = $this->connection->prepare(
                'SELECT * FROM comments WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string) $uuid,
        ]);
        return $this->getComment($statement, $uuid);
    }

    public function getComment(PDOStatement $statement, string $uuid): Comment {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
// Бросаем исключение, если пользователь не найден
        if ($result === false) {
            $message = "Cannot found comment: $uuid";
            $this->logger->warning($message);
            throw new CommentNotFoundException($message);
        }
        $usersRepository = new SqliteUsersRepository($this->connection);
        $postsRepository = new SqlitePostsRepository($this->connection);
        return new Comment(
                new UUID($result['uuid']),
                $usersRepository->get(new UUID($result['author_uuid'])),
                $postsRepository->get(new UUID($result['post_uuid'])),
                $result['text']
        );
    }

}
