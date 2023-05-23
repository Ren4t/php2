<?php

namespace Habr\Renat\UnitTests\Blog\Repositories\PostRepository;

use Habr\Renat\Blog\Exceptions\PostNotFoundException;
use Habr\Renat\Blog\Post;
use Habr\Renat\Blog\Repositories\PostRepository\SqlitePostsRepository;
use Habr\Renat\Blog\User;
use Habr\Renat\Blog\UUID;
use Habr\Renat\Person\Name;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class SqlitePostsRepositoryTest extends TestCase {

    public function testItThrowsAnExceptionWhenPostNotFound(): void {

        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqlitePostsRepository($connectionMock);

        $this->expectExceptionMessage('Cannot get post: c3f85f5e-5168-48bf-ab5a-914ae261c957');
        $this->expectException(PostNotFoundException::class);
        $repository->get(new UUID("c3f85f5e-5168-48bf-ab5a-914ae261c957"));
    }

    public function testItSavePostToDatabase(): void {

        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
                ->expects($this->once())// ожидаем, что будет вызван один раз
                ->method('execute') //    метод execute
                ->with([//               c единственным аргументом - массивом
                    ':uuid' => 'c3f85f5e-5168-48bf-ab5a-914ae261c957',
                    ':author_uuid' => 'c3f85f5e-5168-48bf-ab5a-914ae261c957',
                    ':title' => 'some_title',
                    ':text' => 'some_text'
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqlitePostsRepository($connectionStub);

        $user = new User(
                new UUID('c3f85f5e-5168-48bf-ab5a-914ae261c957'),
                new Name('first_name', 'last_name'),
                'username'
        );

        $repository->save(
                new Post(
                        new UUID('c3f85f5e-5168-48bf-ab5a-914ae261c957'),
                        $user,
                        'some_title',
                        'some_text'
                )
        );
    }

    public function testItGetPostByUuidMethodOne(): void {

        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->method('fetch')->willReturn([
            'uuid' => 'c3f85f5e-5168-48bf-ab5a-914ae261c957',
            'author_uuid' => 'c3f85f5e-5168-48bf-ab5a-914ae261c957',
            'title' => 'some_title',
            'text' => 'some_text',
            'username' =>'ivan66',
            'first_name' => 'Ivan',
            'last_name' => 'Mock'
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqlitePostsRepository($connectionStub);
        $post = $repository->get(new UUID('c3f85f5e-5168-48bf-ab5a-914ae261c957'));

        $this->assertSame('c3f85f5e-5168-48bf-ab5a-914ae261c957', (string) $post->uuid());
    }
    
    public function testItGetPostByUuidMethodTwo(): void {

        $connectionMock = $this->createStub(PDO::class);
        $statementStubPost = $this->createStub(PDOStatement::class);
        $statementStubUser = $this->createStub(PDOStatement::class);

        $statementStubPost->method('fetch')->willReturn([
            'uuid' => 'c3f85f5e-5168-48bf-ab5a-914ae261c957',
            'author_uuid' => 'c3f85f5e-5168-48bf-ab5a-914ae261c957',
            'title' => 'some_title',
            'text' => 'some_text',
        ]);
        
        $statementStubUser->method('fetch')->willReturn([
            'uuid' => 'd3f85f5e-5168-48bf-ab5a-914ae261c957',
            'username' =>'ivan66',
            'first_name' => 'Ivan',
            'last_name' => 'Mock'
        ]);

        $connectionMock->method('prepare')->willReturn($statementStubPost, $statementStubUser);//перебор по порядку

        $repository = new SqlitePostsRepository($connectionMock);
        $post = $repository->get(new UUID('c3f85f5e-5168-48bf-ab5a-914ae261c957'));

        $this->assertSame('c3f85f5e-5168-48bf-ab5a-914ae261c957', (string) $post->uuid());
    }

}
