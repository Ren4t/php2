<?php

namespace Habr\Renat\Blog\Repositories\PostRepository;

use Habr\Renat\Blog\UUID;
use Habr\Renat\Blog\Post;

interface PostsRepositoryInterface {

    public function save(Post $post): void;

    public function get(UUID $uuid): Post;
}
