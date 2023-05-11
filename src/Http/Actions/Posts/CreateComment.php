<?php

namespace Habr\Renat\Http\Actions\Posts;

use Habr\Renat\Blog\Comment;
use Habr\Renat\Blog\Exceptions\HttpException;
use Habr\Renat\Blog\Exceptions\InvalidArgumentException;
use Habr\Renat\Blog\Exceptions\UserNotFoundException;
use Habr\Renat\Blog\Repositories\CommentRepository\CommentsRepositoryInterface;
use Habr\Renat\Blog\Repositories\PostRepository\PostsRepositoryInterface;
use Habr\Renat\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Habr\Renat\Blog\UUID;
use Habr\Renat\Http\Actions\ActionInterface;
use Habr\Renat\Http\ErrorResponse;
use Habr\Renat\Http\Request;
use Habr\Renat\Http\Response;
use Habr\Renat\Http\SuccessfulResponse;

class CreateComment implements ActionInterface {

    // Внедряем репозитории статей и пользователей
    public function __construct(
            private CommentsRepositoryInterface $commentsRepository,
            private PostsRepositoryInterface $postsRepository,
            private UsersRepositoryInterface $usersRepository,
    ) {
        
    }

    public function handle(Request $request): Response {
// Пытаемся создать UUID пользователя из данных запроса
        try {
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
// Пытаемся найти пользователя в репозитории
        try {
            $post = $this->postsRepository->get($postUuid);
            $user = $this->usersRepository->get($authorUuid);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
// Генерируем UUID для новой статьи
        $newCommentUuid = UUID::random();
        try {
// Пытаемся создать объект статьи
// из данных запроса
            $comment = new Comment(
                    $newCommentUuid,
                    $user,
                    $post,
                    $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
// Сохраняем новую статью в репозитории
        $this->commentsRepository->save($comment);
// Возвращаем успешный ответ,
// содержащий UUID новой статьи
        return new SuccessfulResponse([
            'uuid' => (string) $newCommentUuid,
        ]);
    }

}