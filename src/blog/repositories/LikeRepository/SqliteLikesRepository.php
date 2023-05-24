<?php

namespace Habr\Renat\Blog\Repositories\LikeRepository;

use Habr\Renat\Blog\Exceptions\LikesNotFoundException;
use Habr\Renat\Blog\Like;
use Habr\Renat\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Habr\Renat\Blog\Repositories\UserRepository\SqliteUsersRepository;
use Habr\Renat\Blog\UUID;
use PDO;
use PDOStatement;

class SqliteLikesRepository implements LikesRepositoryInterface {

    public function __construct(
            private PDO $connection
    ) {
        
    }

    public function save(Like $like): void {
        $statement = $this->connection->prepare(
                "INSERT INTO `likes` (uuid, post_uuid,author_uuid) VALUES (:uuid, :post_uuid, :author_uuid)"
        );
        $statement->execute([
            ':uuid' => (string) $like->uuid(),
            ':post_uuid' => (string) $like->post()->uuid(),
            ':author_uuid' => (string) $like->user()->uuid(),
        ]);
    }

    public function getByPostUuid(UUID $uuid): array {
        $statement = $this->connection->prepare(
                'SELECT * FROM `likes` WHERE post_uuid = :post_uuid'
        );
        $statement->execute([
            ':post_uuid' => (string) $uuid,
        ]);
        return $this->getLikes($statement, $uuid);
    }

    public function getLikes(PDOStatement $statement, string $uuid): array {
        $resultArray = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (!$resultArray) {
            throw new LikesNotFoundException(
                            "Cannot get likes: $uuid"
            );
        }
        $usersRepository = new SqliteUsersRepository($this->connection);
        $postsRepository = new SqlitePostsRepository($this->connection);
        $post = $postsRepository->get(new UUID($uuid));
        $likes = [];
        foreach ($resultArray as $result) {
            $likes[] = new Like(
                    uuid: new UUID($result['uuid']),
                    post: $post,
                    user: $usersRepository->get(new UUID($result['author_uuid']))
            );
        }
        return $likes;
    }

}
