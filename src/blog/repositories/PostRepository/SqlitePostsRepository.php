<?php

namespace Habr\Renat\Blog\Repositories\PostRepository;

use Habr\Renat\Blog\Exceptions\PostNotFoundException;
use Habr\Renat\Blog\Post;
use Habr\Renat\Blog\Repositories\PostRepository\PostsRepositoryInterface;
use Habr\Renat\Blog\Repositories\UserRepository\SqliteUsersRepository;
use Habr\Renat\Blog\UUID;
use PDO;
use PDOStatement;

class SqlitePostsRepository implements PostsRepositoryInterface{
    
    public function __construct(
            private PDO $connection
    ) {
        
    }
    

    public function save(Post $post): void {
        //Подготавливаем запрос
        $statement = $this->connection->prepare(
                "INSERT INTO posts (uuid, author_uuid, title, text) VALUES (:uuid, :author_uuid, :title, :text)"
        );
// Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => $post->uuid(),
            ':author_uuid' => $post->user()->uuid(),
            ':title' => $post->title(),
            ':text' => $post->text()
        ]);
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
            throw new PostNotFoundException(
                            "Cannot get post: $uuid"
            );
        }
        $usersRepository = new SqliteUsersRepository($this->connection);
        return new Post(
                new UUID($result['uuid']),
                $usersRepository->get(new UUID($result['author_uuid'])),
                $result['title'],
                $result['text']
        );
    }
    
    public function delete(UUID $uuid) {
        
        $statement = $this->connection->prepare(
                'DELETE FROM posts WHERE posts.uuid=:uuid;'
        );
        
        $statement->execute([
            ':uuid' =>$uuid
        ]);
    }
}
