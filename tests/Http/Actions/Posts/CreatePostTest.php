<?php

namespace Habr\Renat\UnitTests\Http\Action\Posts;

use Habr\Renat\Blog\Exceptions\UserNotFoundException;
use Habr\Renat\Blog\Post;
use Habr\Renat\Blog\Repositories\PostRepository\PostsRepositoryInterface;
use Habr\Renat\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Habr\Renat\Blog\User;
use Habr\Renat\Blog\UUID;
use Habr\Renat\Http\Actions\Posts\CreatePost;
use Habr\Renat\Http\Request;
use Habr\Renat\Http\SuccessfulResponse;
use Habr\Renat\Person\Name;
use PHPUnit\Framework\TestCase;

class CreatePostTest extends TestCase {

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsSuccessfulResponse(): void {
        $request = new Request(['username' => 'ivan'], [], '{"author_uuid": "c7b0aada-fdab-4e4a-8c8b-5399956fa670",
"title": "some_title",
"text": "some text"}');

        $usersRepository = $this->usersRepository([
            new User(
                    new UUID('c7b0aada-fdab-4e4a-8c8b-5399956fa670'),
                    new Name('Ivan', 'Nikitin'),
                    'ivan'
            ),
        ]);
        $postsRepository = $this->postsRepository([]);
        $action = new CreatePost($postsRepository, $usersRepository);
        $response = $action->handle($request);
//        $actual = $request->jsonBodyField("author_uuid");
//        $this->assertEquals("c7b0aada-fdab-4e4a-8c8b-5399956fa670", $actual);
        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $response->send();
    }

    private function postsRepository(array $posts): PostsRepositoryInterface {
        return new class($posts) implements PostsRepositoryInterface {

            public function __construct(
                    private array $posts
            ) {
                
            }

            public function get(UUID $uuid): Post {
                
            }

            public function save(Post $post): void {
                
            }
        };
    }

    private function usersRepository(array $users): UsersRepositoryInterface {
// В конструктор анонимного класса передаём массив пользователей
        return new class($users) implements UsersRepositoryInterface {

            public function __construct(
                    private array $users
            ) {
                
            }

            public function save(User $user): void {
                
            }

            public function get(UUID $uuid): User {
                foreach ($this->users as $user) {
                    if ($user instanceof User && (string)$uuid === (string)$user->uuid()) {
                        return $user;
                    }
                }
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User {
                foreach ($this->users as $user) {
                    if ($user instanceof User && $username === $user->username()) {
                        return $user;
                    }
                }
                throw new UserNotFoundException("Not found");
            }
        };
    }

}
