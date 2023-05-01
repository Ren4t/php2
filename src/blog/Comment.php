<?php

namespace Habr\Renat\Blog;

use Habr\Renat\Blog\{
    Post,
    User
};

class Comment {
    
    public function __construct(
            private UUID $uuid,
            private User $user,
            private Post $post,
            private string $text
    ) {
        
    }
    
    public function uuid(): UUID {
        return $this->uuid;
    }

    public function user(): User {
        return $this->user;
    }

    public function post(): Post {
        return $this->post;
    }

    public function text(): string {
        return $this->text;
    }

        
    public function __toString() {
        return "$this->post\n   комментарий:\n$this->description\n(автор: $this->user->getUsername())" . PHP_EOL;
    }
}
