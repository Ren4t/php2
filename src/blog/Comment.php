<?php

namespace Habr\Renat\blog;

use Habr\Renat\blog\{
    Post,
    User
};

class Comment {
    
    public function __construct(
            private int $id,
            private User $user,
            private Post $post,
            private string $description
    ) {
        
    }
    
    public function __toString() {
        return "$this->post\n   комментарий:\n$this->description\n(автор: $this->user->getUsername())" . PHP_EOL;
    }
}
