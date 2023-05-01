<?php

namespace Habr\Renat\Blog\Repositories\CommentRepository;

use Habr\Renat\Blog\Repositories\UserRepository\SqliteUsersRepository;
use Habr\Renat\Blog\Repositories\PostRepository\SqlitePostRepository;
use Habr\Renat\Blog\Comment;
use Habr\Renat\Blog\Post;
use Habr\Renat\Blog\User;
use Habr\Renat\Blog\UUID;
use \PDO;
use \PDOStatement;

class SqlCommentRepository implements CommentsRepositoryInterface{
    
    public function __construct(
            private PDO $connection
    ) {
        
    }

    public function save(Comment $comment): void {
        $statement = $this->connection->prepare(
                "INSERT INTO comments (uuid, post_uuid, author_uuid, text) VALUES (:uuid, :post_uuid, :author_uuid, :text)"
        );
// Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => $comment->uuid(),
            ':post_uuid' => $comment->post()->uuid(),
            ':author_uuid' => $comment->post()->user()->uuid(),
            ':text' => $comment->text()
        ]);
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

    public function getComment(PDOStatement $statement, string $errorString): Comment {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
// Бросаем исключение, если пользователь не найден
        if ($result === false) {
            throw new CommentNotFoundException(
                            "Cannot get comment: $errorString"
            );
        }
        $userRepository = new SqliteUsersRepository($this->connection);
        $postRepository = new SqlitePostRepository($this->connection);
        return new Comment(
                new UUID($result['uuid']),
                $userRepository->get(new UUID($result['author_uuid'])),
                $postRepository->get(new UUID($result['post_uuid'])),
                $result['text']
        );
    }

}
