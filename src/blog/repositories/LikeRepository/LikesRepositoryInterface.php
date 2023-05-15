<?php

namespace Habr\Renat\Blog\Repositories\LikeRepository;

use Habr\Renat\Blog\Like;
use Habr\Renat\Blog\Post;
use Habr\Renat\Blog\UUID;

interface LikesRepositoryInterface {
    
    public function save(Like $like) : void;
    
    public function getByPostUuid(UUID $uuid) : array;
}
