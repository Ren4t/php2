<?php

namespace Habr\Renat\Http\Actions\Posts;

use Habr\Renat\Blog\Exceptions\HttpException;
use Habr\Renat\Blog\Exceptions\InvalidArgumentException;
use Habr\Renat\Blog\Exceptions\UserNotFoundException;
use Habr\Renat\Blog\Like;
use Habr\Renat\Blog\Repositories\LikeRepository\LikesRepositoryInterface;
use Habr\Renat\Blog\Repositories\PostRepository\PostsRepositoryInterface;
use Habr\Renat\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Habr\Renat\Blog\UUID;
use Habr\Renat\Http\Actions\ActionInterface;
use Habr\Renat\Http\ErrorResponse;
use Habr\Renat\Http\Request;
use Habr\Renat\Http\Response;
use Habr\Renat\Http\SuccessfulResponse;

class CreateLike implements ActionInterface{
    public function __construct(
            private LikesRepositoryInterface $likesRepository,
            private PostsRepositoryInterface $postsRepository,
            private UsersRepositoryInterface $usersRepository,
    ) {
        
    }

    public function handle(Request $request): Response {
        try {
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        try {
            $post = $this->postsRepository->get($postUuid);
            $user = $this->usersRepository->get($authorUuid);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $newLikeUuid = UUID::random();
        
        try {
// Пытаемся создать объект лайк
// из данных запроса
            $like = new Like(
                    $newLikeUuid,
                    $post,
                    $user,
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        
        $this->likesRepository->save($like);
// Возвращаем успешный ответ,
// содержащий UUID нового лайка
        return new SuccessfulResponse([
            'uuid' => (string) $newLikeUuid,
        ]);
    }

}
