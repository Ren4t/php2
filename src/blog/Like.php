<?php

namespace Habr\Renat\Blog;

class Like {
    public function __construct(
            private UUID $uuid,
            private Post $post,
            private User $user
    ) {
        
    }
    
    public function uuid(): UUID {
        return $this->uuid;
    }

    public function post(): Post {
        return $this->post;
    }

    public function user(): User {
        return $this->user;
    }


}
