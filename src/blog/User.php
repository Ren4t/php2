<?php

namespace Habr\Renat\Blog;

use Habr\Renat\Person\Name;

class User {

    public function __construct(
            private UUID $uuid,
            private Name $name,
            private string $username,
    ) {
        
    }
    
    public function uuid(): UUID {
        return $this->uuid;
    }
    public function name(): Name {
        return $this->name;
    }
    public function username(): string {
        return $this->username;
        
    }

        
    public function __toString(): string{
        return "Юзер с id $this->uuid с именем $this->name и логином $this->username." . PHP_EOL;
    }

}
