<?php

namespace Habr\Renat\Http\Actions\Posts;

use Habr\Renat\Blog\Exceptions\HttpException;
use Habr\Renat\Blog\Exceptions\UserNotFoundException;
use Habr\Renat\Blog\Repositories\PostRepository\PostsRepositoryInterface;
use Habr\Renat\Blog\UUID;
use Habr\Renat\Http\Actions\ActionInterface;
use Habr\Renat\Http\ErrorResponse;
use Habr\Renat\Http\Request;
use Habr\Renat\Http\Response;
use Habr\Renat\Http\SuccessfulResponse;


class FindByUuid implements ActionInterface{
    
    public function __construct(
            private PostsRepositoryInterface $postsRepository
    ) {
        
    }
    
    public function handle(Request $request): Response {
        try {
            $postuuid = $request->query('postuuid');
        } catch (HttpException $ex) {
            return new ErrorResponse($ex->getMessage());
        }
        
        try{
            $post = $this->postsRepository->get(new UUID($postuuid));
        } catch (UserNotFoundException $ex) {
            return new ErrorResponse($ex->getMessage());
        }
        
        return new SuccessfulResponse([
            'name' => $post->user()->name()->first() . ' ' . $post->user()->name()->last(),
            'title' => $post->title(),
            'post' => $post->text()
        ]);
    }

}
