<?php

namespace Habr\Renat\Blog\Repositories\CommentRepository;

use Habr\Renat\Blog\UUID;
use Habr\Renat\Blog\Comment;

interface CommentsRepositoryInterface {

    public function save(Comment $comment): void;

    public function get(UUID $uuid): Comment;
}
