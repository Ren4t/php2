<?php

namespace Habr\Renat\Http\Actions\Posts;

use Habr\Renat\Blog\Exceptions\PostNotFoundException;
use Habr\Renat\Blog\Repositories\PostRepository\PostsRepositoryInterface;
use Habr\Renat\Blog\UUID;
use Habr\Renat\Http\Actions\ActionInterface;
use Habr\Renat\Http\ErrorResponse;
use Habr\Renat\Http\Request;
use Habr\Renat\Http\Response;
use Habr\Renat\Http\SuccessfulResponse;

class DeletePost implements ActionInterface{
    
    public function __construct(
            private PostsRepositoryInterface $postRepository
    ) {
        
    }
    
    public function handle(Request $request): Response {
        
        try{
            $postUuid = $request->query('uuid');
            $this->postRepository->get(new UUID($postUuid));
        } catch (PostNotFoundException $ex) {
            return new ErrorResponse($ex->getMessage());
        }
        
        $this->postRepository->delete(new UUID($postUuid));
        
        return new SuccessfulResponse([
            'uuid' => $postUuid
        ]);
    }

}
